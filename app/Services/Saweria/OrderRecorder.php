<?php

namespace App\Services\Saweria;

use App\Models\Order;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Throwable;

/*
 * Menyimpan pesanan dari callback Saweria.
 *
 * Callback hanya membawa id, nominal, dan identitas pembeli -- tidak ada nama
 * game maupun paket yang dibeli. Detail itu diambil terpisah lewat endpoint
 * tracking-order, memakai id yang sama.
 */
class OrderRecorder
{
    /*
     * Sengaja memakai SaweriaClient (data mentah), bukan OrderTrackingService.
     * Service tersebut memformat tanggal dan status untuk ditampilkan
     * ("16/09/2026 17:03 WIB"), sehingga tidak cocok disimpan ke kolom tanggal.
     * Penyimpanan harus lepas dari urusan tampilan.
     */
    public function __construct(protected SaweriaClient $client)
    {
    }

    /**
     * Simpan callback apa adanya lebih dulu.
     *
     * Pencatatan tidak boleh bergantung pada keberhasilan permintaan ke Saweria:
     * kalau endpoint detail sedang bermasalah, pesanannya tetap harus tersimpan
     * dan dilengkapi menyusul oleh perintah terjadwal.
     */
    public function record(array $payload): Order
    {
        $order = Order::firstOrNew(['saweria_id' => (string) $payload['id']]);

        $order->fill([
            'donator_name' => $this->text($payload['donator_name'] ?? null),
            'donator_email' => $this->text($payload['donator_email'] ?? null),
            'message' => $this->text($payload['message'] ?? null, 2000),
            'amount_raw' => $this->intOrNull($payload['amount_raw'] ?? null),
            'payload' => $payload,
        ]);

        if (! $order->exists) {
            $order->ordered_at = $this->date($payload['created_at'] ?? null) ?? Carbon::now();
            $order->state = 'pending';
        }

        $order->save();

        return $order;
    }

    /**
     * Lengkapi pesanan dengan detail produk dan status terkini.
     * Mengembalikan false bila Saweria belum bisa dihubungi.
     */
    public function enrich(Order $order): bool
    {
        try {
            $detail = $this->client->trackingOrder($order->saweria_id);
        } catch (Throwable $e) {
            $order->increment('enrich_attempts');
            Log::warning('Detail pesanan Saweria belum bisa diambil', [
                'saweria_id' => $order->saweria_id,
                'attempts' => $order->enrich_attempts,
                'reason' => $e->getMessage(),
            ]);

            return false;
        }

        if ($detail === null) {
            // Saweria belum mengenali id ini. Biasanya callback tiba lebih dulu
            // daripada catatan pesanannya, jadi dicoba lagi nanti.
            $order->increment('enrich_attempts');

            return false;
        }

        $voucher = is_array($detail['game_voucher'] ?? null) ? $detail['game_voucher'] : [];
        $etc = is_array($detail['etc'] ?? null) ? $detail['etc'] : [];

        $paymentStatus = $this->upper($detail['payment_status'] ?? null);
        $fulfillmentStatus = $this->upper($detail['status'] ?? null);

        $order->fill([
            'game_name' => $this->text($voucher['group_name'] ?? null),
            'product_name' => $this->text($voucher['product_name'] ?? null),
            'cover' => $this->text($voucher['thumbnail'] ?? null, 2048),
            'payment_method' => $this->text($etc['payment_method'] ?? null, 64),
            'state' => $this->state($paymentStatus, $fulfillmentStatus),
            'payment_status' => $paymentStatus,
            'fulfillment_status' => $fulfillmentStatus,
            'product_price' => $this->intOrNull($voucher['selling_price'] ?? null),
            'fee' => $this->intOrNull($detail['vendor_cut'] ?? null),
            'currency' => $this->text($detail['currency'] ?? null, 8),
            'amount_raw' => $this->intOrNull($detail['amount_raw'] ?? null) ?? $order->amount_raw,
            'enriched_at' => Carbon::now(),
        ]);

        if ($ordered = $this->date($detail['created_at'] ?? null)) {
            $order->ordered_at = $ordered;
        }

        // Ini stempel waktu pembayaran, bukan waktu barang dikirim.
        $order->paid_at = $paymentStatus === 'SUCCESS'
            ? $this->date($detail['payment_updated_at'] ?? null)
            : null;

        $order->save();

        return true;
    }

    protected function upper(mixed $value): ?string
    {
        return is_string($value) && trim($value) !== ''
            ? strtoupper(trim($value))
            : null;
    }

    /**
     * Menyimpulkan keadaan pesanan dari dua status terpisah milik Saweria:
     * status pembayaran dan status pengiriman barang.
     */
    protected function state(?string $payment, ?string $fulfillment): string
    {
        if ($payment === 'REFUNDED') {
            return 'refunded';
        }

        if ($payment === 'EXPIRED' || $fulfillment === 'EXPIRED') {
            return 'expired';
        }

        $bad = ['FAILED', 'FAILURE', 'CANCELLED', 'CANCELED'];

        if (in_array($payment, $bad, true) || in_array($fulfillment, $bad, true)) {
            return 'failed';
        }

        if ($payment === 'SUCCESS' && $fulfillment === 'SUCCESS') {
            return 'completed';
        }

        if ($payment === 'SUCCESS') {
            // Sudah dibayar tetapi barang belum tercatat terkirim.
            return 'processing';
        }

        if ($payment === 'PENDING') {
            return 'pending';
        }

        return 'unknown';
    }

    protected function text(mixed $value, int $limit = 255): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : mb_substr($value, 0, $limit);
    }

    protected function intOrNull(mixed $value): ?int
    {
        return is_numeric($value) ? (int) round((float) $value) : null;
    }

    protected function date(mixed $value): ?Carbon
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (Throwable) {
            return null;
        }
    }
}

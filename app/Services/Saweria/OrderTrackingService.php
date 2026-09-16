<?php

namespace App\Services\Saweria;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class OrderTrackingService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('saweria.base_url'), '/');
    }

    /**
     * Fetch a public Saweria tracking record and return only display-safe fields.
     * A null result means Saweria did not find that Track ID.
     */
    public function lookup(string $trackId): ?array
    {
        $response = Http::connectTimeout(3)
            ->timeout(8)
            ->retry(1, 0, null, false)
            ->acceptJson()
            ->withUserAgent('levelupgamehub-order-tracking/1.0')
            ->get("{$this->baseUrl}/game-vouchers/tracking-order/".rawurlencode($trackId));

        if ($response->status() === 404) {
            return null;
        }

        if ($response->failed()) {
            throw new RuntimeException('Saweria order tracking is unavailable.');
        }

        $data = $response->json('data');

        if (! is_array($data)) {
            throw new RuntimeException('Saweria returned an invalid tracking response.');
        }

        $responseId = $data['id'] ?? null;

        if (! is_string($responseId)
            || ! hash_equals(strtolower($trackId), strtolower(trim($responseId)))) {
            throw new RuntimeException('Saweria returned a mismatched tracking response.');
        }

        return $this->normalize($trackId, $data);
    }

    protected function normalize(string $trackId, array $data): array
    {
        $voucher = is_array($data['game_voucher'] ?? null) ? $data['game_voucher'] : [];
        $etc = is_array($data['etc'] ?? null) ? $data['etc'] : [];

        $paymentStatus = $this->status($data['payment_status'] ?? null);
        $fulfillmentStatus = $this->status($data['status'] ?? null);
        $state = $this->state($paymentStatus, $fulfillmentStatus);
        $currency = $this->currency($data['currency'] ?? null);
        $productPrice = $this->amount($voucher['selling_price'] ?? null);
        $total = $this->amount($data['amount_raw'] ?? null);
        $fee = $this->amount($data['vendor_cut'] ?? null);

        if ($currency === null || $productPrice === null || $total === null) {
            throw new RuntimeException('Saweria returned invalid order amounts.');
        }

        // The captured Saweria response establishes vendor_cut as the fee and
        // amount_raw as product price + fee. Omit it if that invariant changes.
        if ($fee !== null && abs(($productPrice + $fee) - $total) > 0.01) {
            $fee = null;
        }

        $paymentUpdatedAt = $paymentStatus === 'SUCCESS'
            ? $this->date($data['payment_updated_at'] ?? null)
            : null;

        return [
            'id' => $trackId,
            'game_name' => $this->text($voucher['group_name'] ?? null),
            'product_name' => $this->text($voucher['product_name'] ?? null),
            'cover' => $this->safeHttpsUrl($voucher['thumbnail'] ?? null),
            'payment_method' => $this->paymentMethodLabel($etc['payment_method'] ?? null),
            'payment_status' => $this->paymentStatusLabel($paymentStatus),
            'fulfillment_status' => $this->fulfillmentStatusLabel($fulfillmentStatus),
            'step' => $this->step($state, $paymentStatus),
            'state' => $state,
            'created_at' => $this->date($data['created_at'] ?? null),
            // This is explicitly a payment timestamp. It must not be presented as
            // the time the voucher or top-up was fulfilled.
            'paid_at' => $paymentUpdatedAt,
            'product_price' => $productPrice,
            'fee' => $fee,
            'total' => $total,
            'currency' => $currency,
        ];
    }

    protected function state(string $paymentStatus, string $fulfillmentStatus): string
    {
        if ($paymentStatus === 'REFUNDED') {
            return 'refunded';
        }

        if ($fulfillmentStatus === 'EXPIRED' || $paymentStatus === 'EXPIRED') {
            return 'expired';
        }

        if (in_array($fulfillmentStatus, ['FAILED', 'FAILURE', 'CANCELLED', 'CANCELED'], true)
            || in_array($paymentStatus, ['FAILED', 'FAILURE', 'CANCELLED', 'CANCELED'], true)) {
            return 'failed';
        }

        if ($fulfillmentStatus === 'SUCCESS' && $paymentStatus === 'SUCCESS') {
            return 'completed';
        }

        if ($fulfillmentStatus === 'PROCESSING'
            || ($fulfillmentStatus === 'PENDING' && $paymentStatus === 'SUCCESS')) {
            return 'processing';
        }

        if ($fulfillmentStatus === 'PENDING' && $paymentStatus === 'PENDING') {
            return 'pending';
        }

        return 'unknown';
    }

    protected function step(string $state, string $paymentStatus): int
    {
        return match ($state) {
            'completed' => 3,
            'processing', 'failed', 'expired', 'refunded', 'unknown' => $paymentStatus === 'SUCCESS' ? 1 : 0,
            default => 0, // Pending payment has not completed the first stage.
        };
    }

    protected function paymentStatusLabel(string $status): string
    {
        return match ($status) {
            'SUCCESS' => 'Pembayaran berhasil',
            'PENDING' => 'Menunggu pembayaran',
            'FAILED', 'FAILURE' => 'Pembayaran gagal',
            'EXPIRED' => 'Pembayaran kedaluwarsa',
            'CANCELLED', 'CANCELED' => 'Pembayaran dibatalkan',
            'REFUNDED' => 'Pembayaran dikembalikan',
            default => 'Status pembayaran belum diketahui',
        };
    }

    protected function fulfillmentStatusLabel(string $status): string
    {
        return match ($status) {
            'SUCCESS' => 'Pesanan selesai',
            'PENDING' => 'Menunggu diproses',
            'PROCESSING' => 'Pesanan sedang diproses',
            'FAILED', 'FAILURE' => 'Pemrosesan gagal',
            'EXPIRED' => 'Pesanan kedaluwarsa',
            'CANCELLED', 'CANCELED' => 'Pesanan dibatalkan',
            default => 'Status pemrosesan belum diketahui',
        };
    }

    protected function paymentMethodLabel(mixed $method): string
    {
        $method = strtolower(trim(is_string($method) ? $method : ''));
        $method = str_replace(['-', ' '], '_', $method);

        return match ($method) {
            'qris' => 'QRIS',
            'gopay' => 'GoPay',
            'ovo' => 'OVO',
            'dana' => 'DANA',
            'shopeepay', 'shopee_pay' => 'ShopeePay',
            'linkaja', 'link_aja' => 'LinkAja',
            'virtual_account', 'bank_transfer' => 'Transfer bank',
            'credit_card', 'card' => 'Kartu kredit/debit',
            'convenience_store', 'retail' => 'Gerai retail',
            default => 'Metode pembayaran lainnya',
        };
    }

    protected function status(mixed $status): string
    {
        return is_string($status) ? strtoupper(trim($status)) : '';
    }

    protected function text(mixed $value): string
    {
        return is_string($value) ? trim($value) : '';
    }

    protected function amount(mixed $value): int|float|null
    {
        if (! is_numeric($value) || ! is_finite((float) $value) || (float) $value < 0) {
            return null;
        }

        $amount = (float) $value;

        return floor($amount) === $amount ? (int) $amount : $amount;
    }

    protected function currency(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $currency = strtoupper(trim($value));

        return preg_match('/^[A-Z]{3}$/', $currency) === 1 ? $currency : null;
    }

    protected function date(mixed $value): ?string
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        try {
            return Carbon::parse($value)
                ->setTimezone('Asia/Jakarta')
                ->format('d/m/Y H:i').' WIB';
        } catch (\Throwable) {
            return null;
        }
    }

    protected function safeHttpsUrl(mixed $value): ?string
    {
        if (! is_string($value) || filter_var($value, FILTER_VALIDATE_URL) === false) {
            return null;
        }

        $parts = parse_url($value);

        if (($parts['scheme'] ?? null) !== 'https'
            || empty($parts['host'])
            || isset($parts['user'])
            || isset($parts['pass'])) {
            return null;
        }

        return $value;
    }
}

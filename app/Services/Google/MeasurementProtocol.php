<?php

namespace App\Services\Google;

use App\Models\Order;
use App\Models\TrackingEvent;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/*
 * Mengirim event purchase ke GA4 dari sisi server.
 *
 * Alasannya: pembayaran selesai di halaman Saweria dan pembeli tidak pernah
 * kembali ke situs kita, sehingga peramban tidak punya kesempatan mengirim
 * purchase. Tanpa ini, funnel GA4 berhenti di begin_checkout dan penjualan
 * terlihat nol selamanya.
 *
 * Agar GA4 menempelkan pembelian pada kampanye yang benar, dipakai client id
 * dari cookie _ga milik pengunjung, yang disimpan saat ia menekan checkout.
 * Tanpa client id itu, GA4 akan menganggapnya pengguna baru tanpa sumber.
 */
class MeasurementProtocol
{
    protected const ENDPOINT = 'https://www.google-analytics.com/mp/collect';

    public function isConfigured(): bool
    {
        return ! empty(config('google.ga4.api_secret'))
            && ! empty(config('google.ga4.measurement_id'));
    }

    public function purchase(Order $order): bool
    {
        if (! $this->isConfigured() || $order->state === 'pending') {
            return false;
        }

        $clientId = $this->clientId($order);

        if ($clientId === null) {
            // Tanpa client id, event akan tercatat sebagai pengguna asing tanpa
            // sumber trafik. Itu justru mengotori laporan, jadi dilewati saja.
            return false;
        }

        try {
            $response = Http::timeout(10)->post(self::ENDPOINT.'?'.http_build_query([
                'measurement_id' => config('google.ga4.measurement_id'),
                'api_secret' => config('google.ga4.api_secret'),
            ]), [
                'client_id' => $clientId,
                'non_personalized_ads' => true,
                'events' => [[
                    'name' => 'purchase',
                    'params' => array_filter([
                        // transaction_id membuat GA4 membuang kiriman ganda
                        'transaction_id' => $order->saweria_id,
                        'value' => (float) $order->amount_raw,
                        'currency' => $order->currency ?: 'IDR',
                        'checkout_provider' => 'saweria',
                        'items' => [array_filter([
                            'item_id' => $order->product_name,
                            'item_name' => $order->product_name,
                            'item_category' => $order->game_name,
                            'price' => (float) $order->product_price,
                            'quantity' => 1,
                        ])],
                    ]),
                ]],
            ]);

            return $response->successful();
        } catch (Throwable $e) {
            Log::warning('Pengiriman purchase ke GA4 gagal.', [
                'saweria_id' => $order->saweria_id,
                'reason' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Client id GA4 diambil dari peristiwa checkout milik kunjungan yang
     * dihubungkan dengan pesanan ini.
     */
    protected function clientId(Order $order): ?string
    {
        if (! $order->session_id) {
            return null;
        }

        $event = TrackingEvent::where('session_id', $order->session_id)
            ->where('name', TrackingEvent::CHECKOUT_CLICK)
            ->latest('occurred_at')
            ->first();

        $clientId = $event?->meta['ga_client_id'] ?? null;

        return is_string($clientId) && $clientId !== '' ? $clientId : null;
    }
}

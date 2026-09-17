<?php

namespace App\Services\Google;

use App\Services\Analytics\FunnelReport;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

/*
 * Menyatukan tiga sumber angka menjadi satu bacaan.
 *
 *   Search Console  -> apa yang terjadi SEBELUM klik (impresi, kata kunci, posisi)
 *   GA4             -> apa yang terjadi DI DALAM situs (sesi, keterlibatan, event)
 *   Basis data kita -> apa yang terjadi SETELAH checkout (pesanan, nilai, komisi)
 *
 * Ketiganya tidak akan pernah sama persis, dan itu wajar. GA4 kehilangan
 * pengunjung yang memblokir skrip, Search Console tertinggal beberapa hari dan
 * menyembunyikan kata kunci berfrekuensi rendah, sedangkan basis data kita
 * mencatat semua kunjungan tetapi tidak tahu apa pun tentang pencarian.
 * Karena itu tiap angka ditampilkan bersama sumbernya, bukan dilebur.
 */
class InsightsReport
{
    public function __construct(
        protected Ga4Client $ga4,
        protected SearchConsoleClient $gsc,
        protected ServiceAccountToken $auth
    ) {
    }

    public function build(int $days): array
    {
        $own = FunnelReport::forDays($days)->summary();

        $ga4 = $this->safely('ga4', $days, fn () => [
            'summary' => $this->ga4->summary($days),
            'channels' => $this->ga4->channels($days),
            'events' => $this->ga4->events($days),
            'pages' => $this->ga4->pages($days),
        ], $this->ga4->isConfigured());

        $gsc = $this->safely('gsc', $days, fn () => [
            'summary' => $this->gsc->summary($days),
            'queries' => $this->gsc->queries($days),
            'pages' => $this->gsc->pages($days),
        ], $this->gsc->isConfigured());

        return [
            'own' => $own,
            'ga4' => $ga4,
            'gsc' => $gsc,
            'combined' => $this->combined($own, $ga4, $gsc),
            'service_account' => $this->auth->clientEmail(),
        ];
    }

    /**
     * Rantai lengkap dari pencarian Google sampai pesanan dibayar.
     *
     * Tiap langkah diberi tanda sumbernya, karena melompat antar sumber data
     * berarti angkanya tidak sebanding satu-satu.
     */
    protected function combined(array $own, array $ga4, array $gsc): array
    {
        $steps = [];

        if ($gsc['connected'] ?? false) {
            $steps[] = ['label' => 'Muncul di pencarian', 'value' => $gsc['summary']['impressions'], 'source' => 'Search Console'];
            $steps[] = ['label' => 'Diklik dari pencarian', 'value' => $gsc['summary']['clicks'], 'source' => 'Search Console'];
        }

        if ($ga4['connected'] ?? false) {
            $steps[] = ['label' => 'Sesi di situs', 'value' => $ga4['summary']['sessions'], 'source' => 'GA4'];
        }

        $steps[] = ['label' => 'Kunjungan tercatat', 'value' => $own['sessions'], 'source' => 'Basis data'];
        $steps[] = ['label' => 'Menuju checkout', 'value' => $own['checkout_clicks'], 'source' => 'Basis data'];
        $steps[] = ['label' => 'Pesanan dibayar', 'value' => $own['orders_paid'], 'source' => 'Basis data'];

        $first = $steps[0]['value'] ?? 0;
        $previous = null;

        foreach ($steps as $i => $step) {
            $steps[$i]['of_first'] = $first > 0 ? round($step['value'] / $first * 100, 2) : 0.0;
            $steps[$i]['of_previous'] = $previous > 0 ? round($step['value'] / $previous * 100, 1) : null;
            $previous = $step['value'];
        }

        // Konversi utama: dari klik pencarian sampai pesanan dibayar
        $searchClicks = ($gsc['connected'] ?? false) ? (int) $gsc['summary']['clicks'] : 0;

        return [
            'steps' => $steps,
            'search_to_order' => $searchClicks > 0
                ? round($own['orders_paid'] / $searchClicks * 100, 2)
                : null,
            'visit_to_order' => $own['sessions'] > 0
                ? round($own['orders_paid'] / $own['sessions'] * 100, 2)
                : 0.0,
            'checkout_to_order' => $own['checkout_clicks'] > 0
                ? round($own['orders_paid'] / $own['checkout_clicks'] * 100, 1)
                : null,
        ];
    }

    /**
     * Menjalankan pengambilan data tanpa pernah menjatuhkan halaman panel.
     * Kegagalan dikembalikan sebagai pesan, bukan pengecualian.
     */
    protected function safely(string $key, int $days, callable $fetch, bool $configured): array
    {
        if (! $configured) {
            return ['connected' => false, 'error' => null];
        }

        $cacheKey = "google.insights.{$key}.{$days}";
        $minutes = max(1, (int) config('google.cache_minutes', 30));

        try {
            $data = Cache::remember($cacheKey, $minutes * 60, $fetch);

            return array_merge(['connected' => true, 'error' => null], $data);
        } catch (Throwable $e) {
            Log::warning('Pengambilan data Google gagal.', ['sumber' => $key, 'reason' => $e->getMessage()]);

            return ['connected' => false, 'error' => $e->getMessage()];
        }
    }
}

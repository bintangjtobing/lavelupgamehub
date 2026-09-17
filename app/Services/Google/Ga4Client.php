<?php

namespace App\Services\Google;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/*
 * Pembaca GA4 Data API (REST v1beta).
 *
 * Hanya menjalankan runReport. Semua bentuk laporan disusun di sini agar
 * pemanggil cukup meminta angka yang diinginkan.
 */
class Ga4Client
{
    public function __construct(protected ServiceAccountToken $auth)
    {
    }

    public function isConfigured(): bool
    {
        return $this->auth->isConfigured() && ! empty(config('google.ga4.property_id'));
    }

    /**
     * Angka utama GA4: pengguna, sesi, tayangan, durasi, dan rasio keterlibatan.
     */
    public function summary(int $days): array
    {
        $rows = $this->runReport($days, [], [
            'totalUsers', 'newUsers', 'sessions', 'screenPageViews',
            'averageSessionDuration', 'engagementRate', 'bounceRate',
        ]);

        $totals = $rows['totals'] ?? [];

        return [
            'users' => (int) ($totals['totalUsers'] ?? 0),
            'new_users' => (int) ($totals['newUsers'] ?? 0),
            'sessions' => (int) ($totals['sessions'] ?? 0),
            'page_views' => (int) ($totals['screenPageViews'] ?? 0),
            'avg_duration' => (float) ($totals['averageSessionDuration'] ?? 0),
            'engagement_rate' => (float) ($totals['engagementRate'] ?? 0) * 100,
            'bounce_rate' => (float) ($totals['bounceRate'] ?? 0) * 100,
        ];
    }

    /**
     * Saluran trafik menurut GA4 (organic, paid, social, direct, referral).
     */
    public function channels(int $days, int $limit = 10): array
    {
        $result = $this->runReport($days, ['sessionDefaultChannelGroup'], ['sessions', 'totalUsers', 'engagementRate'], $limit);

        return array_map(fn ($row) => [
            'label' => $row['dimensions'][0] ?? '(lainnya)',
            'sessions' => (int) ($row['metrics']['sessions'] ?? 0),
            'users' => (int) ($row['metrics']['totalUsers'] ?? 0),
            'engagement_rate' => (float) ($row['metrics']['engagementRate'] ?? 0) * 100,
        ], $result['rows'] ?? []);
    }

    /**
     * Event yang tercatat GA4, termasuk begin_checkout dan purchase.
     */
    public function events(int $days, int $limit = 15): array
    {
        $result = $this->runReport($days, ['eventName'], ['eventCount'], $limit);

        return array_map(fn ($row) => [
            'name' => $row['dimensions'][0] ?? '-',
            'count' => (int) ($row['metrics']['eventCount'] ?? 0),
        ], $result['rows'] ?? []);
    }

    /**
     * Halaman paling banyak dibuka.
     */
    public function pages(int $days, int $limit = 10): array
    {
        $result = $this->runReport($days, ['pagePath'], ['screenPageViews', 'totalUsers'], $limit);

        return array_map(fn ($row) => [
            'path' => $row['dimensions'][0] ?? '/',
            'views' => (int) ($row['metrics']['screenPageViews'] ?? 0),
            'users' => (int) ($row['metrics']['totalUsers'] ?? 0),
        ], $result['rows'] ?? []);
    }

    protected function runReport(int $days, array $dimensions, array $metrics, int $limit = 100): array
    {
        $propertyId = config('google.ga4.property_id');

        $body = [
            'dateRanges' => [['startDate' => $days.'daysAgo', 'endDate' => 'today']],
            'metrics' => array_map(fn ($m) => ['name' => $m], $metrics),
            'limit' => $limit,
        ];

        if ($dimensions) {
            $body['dimensions'] = array_map(fn ($d) => ['name' => $d], $dimensions);
            $body['orderBys'] = [[
                'metric' => ['metricName' => $metrics[0]],
                'desc' => true,
            ]];
        }

        $response = Http::withToken($this->auth->accessToken())
            ->timeout(20)
            ->post("https://analyticsdata.googleapis.com/v1beta/properties/{$propertyId}:runReport", $body);

        if ($response->failed()) {
            throw new RuntimeException($this->explain($response->status(), $response->json('error.message')));
        }

        return $this->shape($response->json(), $metrics);
    }

    /**
     * Mengubah bentuk balasan GA4 yang berbasis indeks menjadi array bernama.
     */
    protected function shape(array $payload, array $metrics): array
    {
        $rows = [];

        foreach ($payload['rows'] ?? [] as $row) {
            $rows[] = [
                'dimensions' => array_map(fn ($d) => $d['value'] ?? '', $row['dimensionValues'] ?? []),
                'metrics' => $this->pair($metrics, $row['metricValues'] ?? []),
            ];
        }

        return [
            'rows' => $rows,
            'totals' => $this->pair($metrics, $payload['totals'][0]['metricValues'] ?? []),
        ];
    }

    protected function pair(array $names, array $values): array
    {
        $out = [];

        foreach ($names as $i => $name) {
            $out[$name] = $values[$i]['value'] ?? 0;
        }

        return $out;
    }

    protected function explain(int $status, ?string $message): string
    {
        return match ($status) {
            401, 403 => 'GA4 menolak akses. Pastikan email service account sudah ditambahkan '
                .'sebagai Viewer pada properti GA4.',
            404 => 'Properti GA4 tidak ditemukan. Periksa GA4_PROPERTY_ID (angka, bukan G-XXXXXXX).',
            default => 'GA4 Data API gagal (HTTP '.$status.')'.($message ? ': '.$message : '.'),
        };
    }
}

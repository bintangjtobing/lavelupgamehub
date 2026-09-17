<?php

namespace App\Services\Google;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/*
 * Pembaca Search Console API.
 *
 * Catatan penting soal rentang tanggal: data Search Console tertinggal sekitar
 * dua sampai tiga hari. Meminta sampai "hari ini" akan menghasilkan angka yang
 * terlihat anjlok di ujung, padahal datanya memang belum lengkap. Karena itu
 * rentangnya sengaja dihentikan tiga hari sebelum hari ini.
 */
class SearchConsoleClient
{
    protected const LAG_DAYS = 3;

    public function __construct(protected ServiceAccountToken $auth)
    {
    }

    public function isConfigured(): bool
    {
        return $this->auth->isConfigured() && ! empty(config('google.search_console.site_url'));
    }

    /**
     * Total klik, impresi, CTR, dan posisi rata-rata.
     */
    public function summary(int $days): array
    {
        $rows = $this->query($days, []);
        $row = $rows[0] ?? null;

        return [
            'clicks' => (int) ($row['clicks'] ?? 0),
            'impressions' => (int) ($row['impressions'] ?? 0),
            'ctr' => (float) ($row['ctr'] ?? 0) * 100,
            'position' => round((float) ($row['position'] ?? 0), 1),
            'until' => $this->endDate()->translatedFormat('d M Y'),
        ];
    }

    /**
     * Kata kunci yang memunculkan situs di hasil pencarian.
     */
    public function queries(int $days, int $limit = 20): array
    {
        return array_map(fn ($row) => [
            'query' => $row['keys'][0] ?? '-',
            'clicks' => (int) ($row['clicks'] ?? 0),
            'impressions' => (int) ($row['impressions'] ?? 0),
            'ctr' => (float) ($row['ctr'] ?? 0) * 100,
            'position' => round((float) ($row['position'] ?? 0), 1),
        ], $this->query($days, ['query'], $limit));
    }

    /**
     * Halaman yang mendapat klik dari pencarian.
     */
    public function pages(int $days, int $limit = 15): array
    {
        return array_map(function ($row) {
            $url = $row['keys'][0] ?? '';

            return [
                'path' => '/'.ltrim((string) parse_url($url, PHP_URL_PATH), '/'),
                'clicks' => (int) ($row['clicks'] ?? 0),
                'impressions' => (int) ($row['impressions'] ?? 0),
                'ctr' => (float) ($row['ctr'] ?? 0) * 100,
                'position' => round((float) ($row['position'] ?? 0), 1),
            ];
        }, $this->query($days, ['page'], $limit));
    }

    protected function query(int $days, array $dimensions, int $limit = 25): array
    {
        $site = rawurlencode((string) config('google.search_console.site_url'));
        $end = $this->endDate();

        $body = array_filter([
            'startDate' => $end->copy()->subDays(max(1, $days))->toDateString(),
            'endDate' => $end->toDateString(),
            'dimensions' => $dimensions ?: null,
            'rowLimit' => $limit,
        ]);

        $response = Http::withToken($this->auth->accessToken())
            ->timeout(20)
            ->post("https://searchconsole.googleapis.com/webmasters/v3/sites/{$site}/searchAnalytics/query", $body);

        if ($response->failed()) {
            throw new RuntimeException($this->explain($response->status(), $response->json('error.message')));
        }

        return $response->json('rows') ?? [];
    }

    protected function endDate(): Carbon
    {
        return Carbon::now()->subDays(self::LAG_DAYS)->startOfDay();
    }

    protected function explain(int $status, ?string $message): string
    {
        return match ($status) {
            401, 403 => 'Search Console menolak akses. Tambahkan email service account '
                .'sebagai pengguna pada properti Search Console.',
            404 => 'Properti Search Console tidak ditemukan. Situs diverifikasi lewat DNS, '
                .'jadi GSC_SITE_URL harus ditulis "sc-domain:levelupgamehub.com".',
            default => 'Search Console API gagal (HTTP '.$status.')'.($message ? ': '.$message : '.'),
        };
    }
}

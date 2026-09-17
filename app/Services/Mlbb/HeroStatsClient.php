<?php

namespace App\Services\Mlbb;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/*
 * Pembaca peringkat hero Mobile Legends dari API resmi Moonton.
 *
 * Endpoint ini dipakai mobilelegends.com sendiri dan terbuka tanpa kunci.
 * Diuji juga tanpa header Origin maupun Referer, sehingga aman dipanggil
 * dari sisi server.
 *
 * Seperti sumber luar lainnya di proyek ini: kegagalan tidak boleh
 * menjatuhkan halaman produk. Pemanggil menerima null, bukan pengecualian.
 */
class HeroStatsClient
{
    /**
     * @return array{heroes: array, updated_at: int|null}|null
     */
    public function heroes(int $rank, int $days): ?array
    {
        if (! config('mlbb.enabled')) {
            return null;
        }

        $source = config("mlbb.ranges.{$days}.source");
        $ranks = array_keys(config('mlbb.ranks', []));

        if (! $source || ! in_array($rank, $ranks, true)) {
            return null;
        }

        $key = "mlbb.heroes.{$rank}.{$days}";
        $minutes = max(1, (int) config('mlbb.cache_minutes', 30));

        try {
            return Cache::remember($key, $minutes * 60, fn () => $this->fetch($source, $rank));
        } catch (\Throwable) {
            return null;
        }
    }

    protected function fetch(int $source, int $rank): array
    {
        $response = Http::timeout((int) config('mlbb.timeout', 8))
            ->connectTimeout(3)
            ->retry(1, 0, null, false)
            ->acceptJson()
            ->post(config('mlbb.endpoint')."/{$source}", [
                'pageSize' => max(1, (int) config('mlbb.limit', 20)),
                'pageIndex' => 1,
                'filters' => [
                    ['field' => 'bigrank', 'operator' => 'eq', 'value' => (string) $rank],
                    ['field' => 'match_type', 'operator' => 'eq', 'value' => 0],
                ],
                'sorts' => [
                    ['data' => ['field' => 'main_hero_win_rate', 'order' => 'desc'], 'type' => 'sequence'],
                ],
                'fields' => [
                    'main_hero',
                    'main_hero_appearance_rate',
                    'main_hero_ban_rate',
                    'main_hero_win_rate',
                    'main_heroid',
                ],
            ]);

        if ($response->failed() || $response->json('code') !== 0) {
            throw new RuntimeException('Moonton menolak permintaan statistik hero.');
        }

        $records = $response->json('data.records');

        if (! is_array($records)) {
            throw new RuntimeException('Bentuk balasan statistik hero tidak dikenali.');
        }

        return [
            'heroes' => $this->normalize($records),
            'total' => (int) $response->json('data.total'),
        ];
    }

    protected function normalize(array $records): array
    {
        $heroes = [];

        foreach ($records as $record) {
            $hero = $record['data'] ?? null;

            if (! is_array($hero)) {
                continue;
            }

            $name = data_get($hero, 'main_hero.data.name');
            $image = data_get($hero, 'main_hero.data.head');

            if (! is_string($name) || $name === '') {
                continue;
            }

            $heroes[] = [
                'id' => (int) ($hero['main_heroid'] ?? 0),
                'name' => $name,
                // Gambar hanya diterima bila berasal dari CDN Moonton, supaya
                // balasan yang berubah tidak bisa menyisipkan alamat asing.
                'image' => $this->safeImage($image),
                'win_rate' => $this->rate($hero['main_hero_win_rate'] ?? null),
                'pick_rate' => $this->rate($hero['main_hero_appearance_rate'] ?? null),
                'ban_rate' => $this->rate($hero['main_hero_ban_rate'] ?? null),
            ];
        }

        return $heroes;
    }

    protected function safeImage(mixed $url): ?string
    {
        if (! is_string($url) || ! str_starts_with($url, 'https://')) {
            return null;
        }

        $host = parse_url($url, PHP_URL_HOST);

        return is_string($host) && str_ends_with($host, '.youngjoygame.com') ? $url : null;
    }

    /**
     * Moonton mengirim rasio dalam bentuk pecahan (0.588212), bukan persen.
     */
    protected function rate(mixed $value): float
    {
        return is_numeric($value) ? round((float) $value * 100, 2) : 0.0;
    }
}

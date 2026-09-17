<?php

namespace App\Services\GameStats\Drivers;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/*
 * League of Legends lewat Riot Data Dragon.
 *
 * Data Dragon adalah CDN resmi Riot: terbuka, tanpa kunci, dan selalu
 * mengikuti versi patch terbaru. Yang disediakan hanya daftar champion
 * beserta gambar dan perannya.
 *
 * Win rate memang TIDAK ADA di sini, dan itu bukan kelalaian: Riot tidak
 * membuka angka tersebut ke publik, sementara sumber pihak ketiga yang diuji
 * menolak permintaan dari server. Daripada menampilkan angka karangan,
 * kolomnya dikosongkan dan diganti peran champion.
 */
class LolDriver implements StatsDriver
{
    protected const VERSIONS = 'https://ddragon.leagueoflegends.com/api/versions.json';

    protected const ROLES = [
        'Assassin' => 'Assassin',
        'Fighter' => 'Fighter',
        'Mage' => 'Mage',
        'Marksman' => 'Marksman',
        'Support' => 'Support',
        'Tank' => 'Tank',
    ];

    public function rows(int $limit): array
    {
        $version = $this->version();

        $response = $this->request()
            ->get("https://ddragon.leagueoflegends.com/cdn/{$version}/data/en_US/champion.json");

        if ($response->failed()) {
            throw new RuntimeException('Data Dragon tidak bisa dihubungi (HTTP '.$response->status().').');
        }

        $champions = $response->json('data');

        if (! is_array($champions)) {
            throw new RuntimeException('Bentuk balasan Data Dragon tidak dikenali.');
        }

        $rows = [];

        foreach ($champions as $champion) {
            $name = $champion['name'] ?? null;
            $file = $champion['image']['full'] ?? null;

            if (! is_string($name) || $name === '') {
                continue;
            }

            $tag = is_array($champion['tags'] ?? null) ? ($champion['tags'][0] ?? null) : null;

            $rows[] = [
                'name' => $name,
                'image' => is_string($file) && $file !== ''
                    ? "https://ddragon.leagueoflegends.com/cdn/{$version}/img/champion/".rawurlencode($file)
                    : null,
                'win' => null,
                'pick' => null,
                'ban' => null,
                'role' => self::ROLES[$tag] ?? $tag,
            ];
        }

        // Tanpa angka untuk diurutkan, abjad adalah urutan yang paling jujur.
        usort($rows, fn ($a, $b) => strcmp($a['name'], $b['name']));

        return array_slice($rows, 0, $limit);
    }

    protected function version(): string
    {
        $response = $this->request()->get(self::VERSIONS);
        $versions = $response->successful() ? $response->json() : null;

        // Versi terbaru selalu berada di urutan pertama.
        return is_array($versions) && isset($versions[0]) && is_string($versions[0])
            ? $versions[0]
            : '15.1.1';
    }

    protected function request()
    {
        return Http::timeout((int) config('gamestats.timeout', 10))
            ->connectTimeout(3)
            ->retry(1, 0, null, false)
            ->acceptJson();
    }
}

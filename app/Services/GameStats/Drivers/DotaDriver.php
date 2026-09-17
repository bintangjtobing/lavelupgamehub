<?php

namespace App\Services\GameStats\Drivers;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/*
 * Dota 2 lewat OpenDota.
 *
 * API publik, terdokumentasi, tanpa kunci. Angka yang dipakai berasal dari
 * pertandingan publik karena sampelnya besar. OpenDota juga menyimpan data
 * pertandingan pro, tetapi jumlahnya kecil -- ada hero yang hanya dipilih
 * enam kali -- sehingga menggabungkannya ke dalam satu tabel akan
 * menyesatkan. Karena itu ban rate dibiarkan kosong.
 */
class DotaDriver implements StatsDriver
{
    protected const ENDPOINT = 'https://api.opendota.com/api/heroStats';

    protected const IMAGE_HOST = 'https://cdn.cloudflare.steamstatic.com';

    /**
     * Hero dengan sampel kecil dibuang: pada jumlah pertandingan sedikit,
     * win rate berayun liar dan peringkatnya jadi tidak berarti.
     */
    protected const MIN_PICKS = 2000;

    public function rows(int $limit): array
    {
        $response = Http::timeout((int) config('gamestats.timeout', 10))
            ->connectTimeout(3)
            ->retry(1, 0, null, false)
            ->acceptJson()
            ->get(self::ENDPOINT);

        if ($response->failed()) {
            throw new RuntimeException('OpenDota tidak bisa dihubungi (HTTP '.$response->status().').');
        }

        $heroes = $response->json();

        if (! is_array($heroes)) {
            throw new RuntimeException('Bentuk balasan OpenDota tidak dikenali.');
        }

        $totalPicks = 0;
        foreach ($heroes as $hero) {
            $totalPicks += (int) ($hero['pub_pick'] ?? 0);
        }

        $rows = [];

        foreach ($heroes as $hero) {
            $picks = (int) ($hero['pub_pick'] ?? 0);
            $wins = (int) ($hero['pub_win'] ?? 0);
            $name = $hero['localized_name'] ?? null;

            if ($picks < self::MIN_PICKS || ! is_string($name) || $name === '') {
                continue;
            }

            $rows[] = [
                'name' => $name,
                'image' => $this->image($hero['img'] ?? null),
                'win' => round($wins / $picks * 100, 2),
                'pick' => $totalPicks > 0 ? round($picks / $totalPicks * 100, 2) : null,
                'ban' => null,
                'role' => is_array($hero['roles'] ?? null) ? ($hero['roles'][0] ?? null) : null,
            ];
        }

        usort($rows, fn ($a, $b) => $b['win'] <=> $a['win']);

        return array_slice($rows, 0, $limit);
    }

    protected function image(mixed $path): ?string
    {
        if (! is_string($path) || $path === '') {
            return null;
        }

        // OpenDota mengirim jalur relatif terhadap CDN Steam, dan menyisakan
        // tanda tanya di ujungnya.
        return self::IMAGE_HOST.rtrim($path, '?');
    }
}

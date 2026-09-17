<?php

namespace App\Services\GameStats\Drivers;

use App\Services\Mlbb\HeroStatsClient;
use RuntimeException;

/*
 * Mobile Legends lewat API resmi Moonton.
 *
 * Pembacaannya sudah ditangani HeroStatsClient, jadi driver ini hanya
 * menyesuaikan bentuk barisnya agar sama dengan game lain.
 */
class MlbbDriver implements StatsDriver
{
    public function __construct(protected HeroStatsClient $client)
    {
    }

    public function rows(int $limit): array
    {
        $stats = $this->client->heroes(
            (int) config('mlbb.default_rank'),
            (int) config('mlbb.default_range')
        );

        if ($stats === null) {
            throw new RuntimeException('Statistik Moonton sedang tidak tersedia.');
        }

        return array_map(fn ($hero) => [
            'name' => $hero['name'],
            'image' => $hero['image'],
            'win' => $hero['win_rate'],
            'pick' => $hero['pick_rate'],
            'ban' => $hero['ban_rate'],
            'role' => null,
        ], array_slice($stats['heroes'], 0, $limit));
    }
}

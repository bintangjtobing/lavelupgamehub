<?php

namespace App\Services\GameStats;

use App\Models\CatalogItem;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

/*
 * Pintu masuk tunggal ke statistik hero semua game.
 *
 * Setiap pemanggilan dibungkus penyimpanan sementara dan penangkap galat:
 * sumber luar boleh gagal, tetapi halaman yang memakainya tidak boleh ikut
 * jatuh. Game yang gagal dibaca dikembalikan sebagai null, dan tampilan
 * melewatinya begitu saja.
 */
class GameStatsManager
{
    /**
     * Statistik satu game, atau null bila sumbernya sedang tidak bisa dibaca.
     */
    public function game(string $key): ?array
    {
        $config = config("gamestats.games.{$key}");

        if (! config('gamestats.enabled') || ! is_array($config)) {
            return null;
        }

        $limit = max(1, (int) config('gamestats.limit', 30));
        $minutes = max(1, (int) config('gamestats.cache_minutes', 60));

        try {
            $rows = Cache::remember(
                "gamestats.{$key}.{$limit}",
                $minutes * 60,
                fn () => app($config['driver'])->rows($limit)
            );
        } catch (Throwable $e) {
            Log::warning('Statistik game gagal dibaca.', [
                'game' => $key,
                'reason' => $e->getMessage(),
            ]);

            return null;
        }

        if ($rows === []) {
            return null;
        }

        return [
            'key' => $key,
            'label' => $config['label'],
            'source' => $config['source'] ?? null,
            'note' => $config['note'] ?? null,
            'columns' => $config['columns'] ?? ['win', 'pick', 'ban'],
            'cta' => $config['cta'] ?? 'Top Up Sekarang',
            'item' => $this->item($config['slug'] ?? null),
            'rows' => $rows,
        ];
    }

    /**
     * Semua game yang berhasil dibaca, mengikuti urutan di berkas konfigurasi.
     */
    public function all(): array
    {
        $games = [];

        foreach (array_keys(config('gamestats.games', [])) as $key) {
            if ($game = $this->game($key)) {
                $games[] = $game;
            }
        }

        return $games;
    }

    /**
     * Kunci game untuk sebuah produk katalog, bila ada.
     */
    public function keyForSlug(string $slug): ?string
    {
        foreach (config('gamestats.games', []) as $key => $config) {
            if (($config['slug'] ?? null) === $slug) {
                return $key;
            }
        }

        return null;
    }

    /**
     * Produk katalog tujuan tombol top up. Bila produknya tidak ada atau
     * sedang nonaktif, tombolnya tidak ditampilkan daripada menautkan
     * halaman yang tidak bisa dibuka.
     */
    protected function item(?string $slug): ?CatalogItem
    {
        if (! $slug) {
            return null;
        }

        return Cache::remember(
            "gamestats.item.{$slug}",
            600,
            fn () => CatalogItem::active()->where('slug', $slug)->first()
        );
    }
}

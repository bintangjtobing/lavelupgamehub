<?php

use App\Services\GameStats\Drivers\AovDriver;
use App\Services\GameStats\Drivers\DotaDriver;
use App\Services\GameStats\Drivers\LolDriver;
use App\Services\GameStats\Drivers\MlbbDriver;

/*
 * Statistik hero per game.
 *
 * Keempat sumbernya berbeda sifat, dan perbedaan itu sengaja tidak
 * disembunyikan karena memengaruhi seberapa jauh angkanya boleh dipercaya:
 *
 *   MLBB  - API resmi Moonton. Terbuka, stabil, lengkap.
 *   Dota  - OpenDota, API publik. Terbuka dan stabil, tetapi tidak
 *           menyediakan ban rate untuk pertandingan publik.
 *   AOV   - rovmeta.com. Bukan API: angkanya dikupas dari muatan halaman,
 *           sehingga akan patah bila situs itu berubah. Datanya juga dari
 *           pertandingan pro server Thailand, bukan pemain umum.
 *   LoL   - Riot Data Dragon. Resmi, tetapi HANYA daftar champion beserta
 *           gambar dan perannya. Riot tidak membuka win rate ke publik,
 *           dan satu-satunya sumber lain yang diuji menolak permintaan
 *           dari server, jadi kolom rate untuk LoL memang tidak ada.
 */
return [
    'enabled' => (bool) env('GAME_STATS_ENABLED', true),

    'cache_minutes' => (int) env('GAME_STATS_CACHE', 60),

    'timeout' => 10,

    // Berapa baris yang ditampilkan per game
    'limit' => 30,

    'games' => [
        'mlbb' => [
            'driver' => MlbbDriver::class,
            'label' => 'Mobile Legends',
            'slug' => 'mobile-legends-bang-bang',
            'source' => 'Moonton',
            'note' => 'Data resmi Moonton, 7 hari terakhir, semua rank.',
            'columns' => ['win', 'pick', 'ban'],
        ],

        'aov' => [
            'driver' => AovDriver::class,
            'label' => 'Arena of Valor',
            'slug' => 'arena-of-valor',
            'source' => 'rovmeta.com',
            'note' => 'Dari pertandingan pro server Thailand, bukan pemain umum.',
            'columns' => ['win', 'pick', 'ban'],
        ],

        'dota' => [
            'driver' => DotaDriver::class,
            'label' => 'Dota 2',
            // Saweria tidak menjual top up Dota secara langsung; item Dota 2
            // dibeli memakai saldo Steam, jadi tombolnya diarahkan ke sana.
            'slug' => 'steam-voucher-indonesia',
            'cta' => 'Beli Steam Wallet',
            'source' => 'OpenDota',
            'note' => 'Pertandingan publik. Ban rate tidak dibuka untuk pertandingan publik.',
            'columns' => ['win', 'pick'],
        ],

        'lol' => [
            'driver' => LolDriver::class,
            'label' => 'League of Legends',
            'slug' => 'league-of-legends-pc',
            'source' => 'Riot Data Dragon',
            'note' => 'Daftar champion resmi Riot. Riot tidak membuka data win rate ke publik.',
            'columns' => ['role'],
        ],
    ],
];

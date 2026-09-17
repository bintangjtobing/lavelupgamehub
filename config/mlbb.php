<?php

/*
 * Statistik hero Mobile Legends dari Moonton.
 *
 * Sumbernya API resmi yang dipakai mobilelegends.com sendiri, dan terbuka:
 * tidak ada kunci, token, maupun cookie. Cukup satu permintaan POST.
 *
 * Nilai di bawah diambil dari berkas konfigurasi halaman resmi Moonton,
 * bukan hasil terkaan.
 */
return [
    'enabled' => (bool) env('MLBB_STATS_ENABLED', true),

    'endpoint' => 'https://api.gms.moontontech.com/api/gms/source/2669606',

    // Produk katalog yang menampilkan statistik ini
    'slug' => env('MLBB_STATS_SLUG', 'mobile-legends-bang-bang'),

    /*
     * Tiap rentang hari punya sumber datanya sendiri di Moonton.
     * Angka sumber berasal dari berkas hero_predict_{hari}.json pada
     * konfigurasi resmi mereka.
     */
    'ranges' => [
        1 => ['source' => 2756567, 'label' => '1 hari'],
        3 => ['source' => 2756568, 'label' => '3 hari'],
        7 => ['source' => 2756569, 'label' => '7 hari'],
        15 => ['source' => 2756565, 'label' => '15 hari'],
        30 => ['source' => 2756570, 'label' => '30 hari'],
    ],

    'default_range' => 7,

    // Tingkatan rank, mengikuti penamaan resmi Moonton dalam bahasa Indonesia
    'ranks' => [
        101 => 'Semua',
        5 => 'Epic',
        6 => 'Legend',
        7 => 'Mythic',
        8 => 'Mythical Honor',
        9 => 'Mythical Glory+',
    ],

    'default_rank' => 101,

    // Berapa hero yang ditampilkan
    'limit' => 30,

    /*
     * Moonton menyegarkan datanya tiap enam menit. Menyimpan hasilnya lebih
     * lama dari itu tidak membuat angkanya basi, tetapi menjaga halaman produk
     * tetap cepat dan tidak bergantung pada kesehatan server mereka.
     */
    'cache_minutes' => (int) env('MLBB_STATS_CACHE', 30),

    'timeout' => 8,
];

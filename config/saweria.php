<?php

return [
    'base_url' => env('SAWERIA_BASE_URL', 'https://backend.saweria.co'),

    // Batas panggilan API. Detail produk ada di jalur request halaman, jadi harus
    // gagal dengan cepat agar halaman dapat menampilkan status tidak tersedia.
    'connect_timeout' => (int) env('SAWERIA_CONNECT_TIMEOUT', 3),
    'request_timeout' => (int) env('SAWERIA_REQUEST_TIMEOUT', 15),
    'detail_timeout' => (int) env('SAWERIA_DETAIL_TIMEOUT', 8),
    'request_attempts' => (int) env('SAWERIA_REQUEST_ATTEMPTS', 2),
    'retry_delay_ms' => (int) env('SAWERIA_RETRY_DELAY_MS', 250),

    // Akun toko top up yang katalognya disinkronkan
    'username' => env('SAWERIA_USERNAME'),
    'streamer_id' => env('SAWERIA_STREAMER_ID'),

    // Dipakai memvalidasi header Saweria-Callback-Signature
    'stream_key' => env('SAWERIA_STREAM_KEY'),

    // URL publik toko, jadi tujuan link tiap item katalog
    'store_url' => 'https://saweria.co/' . env('SAWERIA_USERNAME') . '/toko-top-up',

    /*
     * Section "Paling Laris".
     *
     * Saweria tidak membuka data penjualan, jadi urutan ini kurasi manual --
     * disusun menurut permintaan pasar Indonesia, bukan angka penjualan kita.
     * Urutan array = urutan tampil di halaman depan.
     *
     * Nanti setelah webhook berjalan dan tabel pesanan terisi, daftar ini bisa
     * diganti dengan peringkat asli berdasarkan jumlah transaksi.
     */
    'best_sellers' => [
        'DG-MOBILELEGENDSBANGBANG',
        'DG-FREEFIRE',
        'DG-PUBGMOBILE',
        'VC-FOLAPLAY',
        'DG-VALORANT',
        'DG-MAGICCHESSGOGO',
        'DG-DELTAFORCEGARENA',
        'VC-ROBLOX',
        'VC-STEAMVOUCHERINDONESIA',
        'VC-GOOGLEPLAYINDONESIA',
    ],

    /*
     * Klasifikasi game vs produk.
     *
     * Saweria TIDAK menyediakan kategori ini. Yang diberikan API hanya "variant",
     * dan itu menandakan cara pemenuhan, bukan jenis barang:
     *
     *   DIGITAL             -> top up langsung, pembeli mengisi User ID / Zone ID
     *   VOUCHER             -> ditebus lewat kode
     *   PREGENERATE_VOUCHER -> kode yang sudah dibuat sebelumnya
     *
     * Jadi jenisnya kita turunkan sendiri. Aturan dasarnya: DIGITAL dianggap game,
     * voucher dianggap produk. Dua daftar di bawah menangani yang menyimpang --
     * misalnya Bigo Live itu DIGITAL tapi bukan game, dan Roblox itu VOUCHER tapi game.
     *
     * Kalau ada item yang salah kelompok, cukup pindahkan kodenya ke daftar yang benar.
     */
    'classification' => [
        // DIGITAL tapi bukan game: aplikasi live streaming, sosial, video
        'force_product' => [
            'DG-BIGOLIVE',
            'DG-DAZZLIVE',
            'DG-HAGO',
            'DG-LIKEE',
            'DG-LITA',
            'DG-LIVU',
            'DG-POPPOLIVE',
            'DG-SUGOVOICELIVECHATPARTY',
            'DG-VIDIO',
            'DG-ZEPETO',
            'VC-BIGOLIVEVOUCHER',
            'VC-BSTATIONBILIBILI',
            'VC-GOOGLEPLAYINDONESIA',
            'VC-IDNAPP',
            'VC-IQIYI',
            'VC-SPOTIFY',
            'VC-TIKTOKCOIN',
            'VC-VISION',
            'VC-VIUPREMIUM',
            'VC-WEBTOONKOIN',
        ],

        // Voucher tapi tetap game: platform atau mata uang game
        'force_game' => [
            'PRE-VC-GARENA',
            'VC-EFOOTBALLPOWEREDBYGOOGLEPLAY',
            'VC-FOLAPLAY',
            'VC-MEGAXUSMICASHVOUCHER',
            'VC-MINECRAFT',
            'VC-NINTENDOESHOPCARDUS',
            'VC-PCGAMEPASS',
            'VC-POINTBLANKVOUCHERCASH',
            'VC-PSNVOUCHERINDONESIA',
            'VC-RIOTGAMESVOUCHER',
            'VC-ROBLOX',
            'VC-STEAMVOUCHERINDONESIA',
            'VC-XBOXUSA',
        ],
    ],
];

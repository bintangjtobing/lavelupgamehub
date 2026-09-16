<?php

return [
    'url' => rtrim(env('SEO_URL', 'https://levelupgamehub.com'), '/'),

    'site_name' => 'LevelUp Market',
    'locale' => 'id_ID',
    'description' => 'Temukan top up game, voucher, dan produk digital yang tersedia di katalog LevelUp Market.',
    'social_image' => 'https://res.cloudinary.com/boxityapp/image/upload/v1730810811/levelupgaming/fiuim0knuvois1udqhnk.png',
    'logo' => '/images/brand/logo.png',

    'publisher' => [
        'name' => 'LevelUp Market',
        'same_as' => [
            'https://www.facebook.com/profile.php?id=61568151335436',
            'https://www.instagram.com/levelupmarketgaming',
        ],
    ],

    'pages' => [
        '/' => [
            'title' => 'Top Up Game & Voucher Digital | LevelUp Market',
            'description' => 'Temukan top up game, voucher, dan hiburan digital di LevelUp Market. Pilih produk, cek paket serta harga terbaru, lalu checkout melalui Saweria.',
            'label' => 'Beranda',
        ],
        '/topup' => [
            'title' => 'Katalog Top Up Game & Voucher | LevelUp Market',
            'description' => 'Cari game atau voucher di katalog LevelUp Market. Filter kategori, lihat produk baru, dan buka pilihan paket beserta harga yang tersedia dari Saweria.',
            'label' => 'Katalog Top Up',
        ],
        '/about' => [
            'title' => 'Tentang LevelUp Market',
            'description' => 'Kenali LevelUp Market, cara kerja katalog produk digital kami, serta alur memilih paket dan melanjutkan pembayaran melalui Saweria.',
            'label' => 'Tentang Kami',
        ],
        '/contact' => [
            'title' => 'Hubungi LevelUp Market',
            'description' => 'Butuh bantuan top up atau voucher? Hubungi LevelUp Market melalui WhatsApp, email, atau formulir kontak untuk pertanyaan produk dan kendala pesanan.',
            'label' => 'Kontak',
        ],
        '/track-order' => [
            'title' => 'Lacak Pesanan Top Up & Voucher | LevelUp Market',
            'description' => 'Lacak pesanan top up dan voucher dengan Track ID Saweria. Periksa status pembayaran, pengiriman, harga produk, dan biaya transaksi di LevelUp Market.',
            'label' => 'Lacak Pesanan',
        ],
        '/faq' => [
            'title' => 'Pertanyaan Umum | LevelUp Market',
            'description' => 'Pelajari cara top up, memilih paket, mengisi data akun, dan checkout di Saweria. Temukan jawaban tentang harga, pelacakan pesanan, dan bantuan LevelUp Market.',
            'label' => 'Pertanyaan Umum',
        ],
        '/privacy-policy' => [
            'title' => 'Kebijakan Privasi | LevelUp Market',
            'description' => 'Ketahui bagaimana LevelUp Market menggunakan data formulir, ulasan, cookie, dan analitik, termasuk pilihan pengguna terkait informasi pribadi.',
            'label' => 'Kebijakan Privasi',
        ],
        '/terms-and-conditions' => [
            'title' => 'Syarat dan Ketentuan | LevelUp Market',
            'description' => 'Baca ketentuan penggunaan LevelUp Market: informasi katalog dan harga, checkout melalui Saweria, tanggung jawab data akun, serta penanganan kendala.',
            'label' => 'Syarat dan Ketentuan',
        ],
    ],
];

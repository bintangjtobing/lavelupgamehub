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
            'description' => 'Jelajahi katalog top up game, voucher, dan produk digital LevelUp Market lalu lanjutkan pembayaran melalui Saweria.',
            'label' => 'Beranda',
        ],
        '/topup' => [
            'title' => 'Katalog Top Up Game & Voucher | LevelUp Market',
            'description' => 'Lihat pilihan top up game, voucher, dan produk digital yang aktif di katalog LevelUp Market.',
            'label' => 'Katalog Top Up',
        ],
        '/about' => [
            'title' => 'Tentang LevelUp Market',
            'description' => 'Kenali LevelUp Market dan layanan katalog top up game, voucher, serta produk digital yang kami tampilkan.',
            'label' => 'Tentang Kami',
        ],
        '/contact' => [
            'title' => 'Hubungi LevelUp Market',
            'description' => 'Hubungi tim LevelUp Market untuk pertanyaan tentang katalog, top up game, voucher, dan produk digital.',
            'label' => 'Kontak',
        ],
        '/track-order' => [
            'title' => 'Lacak Pesanan Top Up & Voucher | LevelUp Market',
            'description' => 'Masukkan Track ID Saweria untuk memeriksa status pembayaran dan pengiriman top up game atau voucher melalui LevelUp Market.',
            'label' => 'Lacak Pesanan',
        ],
        '/faq' => [
            'title' => 'Pertanyaan Umum | LevelUp Market',
            'description' => 'Temukan jawaban atas pertanyaan umum tentang pembelian top up game, voucher, pembayaran, dan bantuan LevelUp Market.',
            'label' => 'Pertanyaan Umum',
        ],
        '/privacy-policy' => [
            'title' => 'Kebijakan Privasi | LevelUp Market',
            'description' => 'Baca kebijakan privasi LevelUp Market mengenai data yang dikumpulkan, digunakan, dan hak pengguna.',
            'label' => 'Kebijakan Privasi',
        ],
        '/terms-and-conditions' => [
            'title' => 'Syarat dan Ketentuan | LevelUp Market',
            'description' => 'Baca syarat dan ketentuan penggunaan situs dan layanan LevelUp Market.',
            'label' => 'Syarat dan Ketentuan',
        ],
    ],
];

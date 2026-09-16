<?php

/*
 * Language map for page titles, descriptions, and concise visible copy.
 * Google Search ignores the meta-keywords tag, so these terms should guide
 * natural page copy rather than be emitted as a ranking signal or repeated.
 */
return [
    'static' => [
        '/' => [
            'primary' => 'top up game dan voucher digital',
            'secondary' => [
                'top up game Indonesia',
                'voucher game Indonesia',
                'katalog produk digital',
                'harga top up game',
            ],
        ],
        '/topup' => [
            'primary' => 'katalog top up game',
            'secondary' => [
                'daftar top up game',
                'voucher game',
                'harga voucher digital',
                'cari top up game',
            ],
        ],
        '/about' => [
            'primary' => 'tentang LevelUp Market',
            'secondary' => [
                'LevelUp Gaming Market',
                'katalog LevelUp Market',
                'cara kerja LevelUp Market',
            ],
        ],
        '/contact' => [
            'primary' => 'hubungi LevelUp Market',
            'secondary' => [
                'kontak LevelUp Market',
                'bantuan top up game',
                'bantuan voucher digital',
            ],
        ],
        '/faq' => [
            'primary' => 'cara top up game',
            'secondary' => [
                'pertanyaan top up game',
                'cara beli voucher game',
                'cara checkout Saweria',
                'bantuan top up game',
            ],
        ],
        '/privacy-policy' => [
            'primary' => 'kebijakan privasi LevelUp Market',
            'secondary' => [
                'data pengguna LevelUp Market',
                'cookie LevelUp Market',
                'privasi formulir kontak',
            ],
        ],
        '/terms-and-conditions' => [
            'primary' => 'syarat dan ketentuan LevelUp Market',
            'secondary' => [
                'syarat penggunaan LevelUp Market',
                'ketentuan katalog top up',
                'ketentuan checkout Saweria',
            ],
        ],
        '/track-order' => [
            'primary' => 'lacak pesanan top up',
            'secondary' => [
                'cek status pesanan top up',
                'lacak pesanan Saweria',
                'cek Track ID Saweria',
                'status voucher game',
            ],
        ],
    ],

    /*
     * Runtime fallback for every active catalog item. Integration should choose
     * the matching $item->category and replace {name} with the stored catalog
     * name. No currency or denomination is inferred by these templates.
     */
    'product_fallback' => [
        'game' => [
            'primary' => 'top up {name}',
            'secondary' => [
                'harga top up {name}',
                'cara top up {name}',
                'paket {name}',
            ],
            'title' => 'Top Up {name} | LevelUp Market',
            'description' => 'Lihat paket dan harga top up {name} yang tersedia di LevelUp Market, lalu lanjutkan pengisian data dan pembayaran melalui Saweria.',
            'intro' => 'Pilih paket {name} yang tersedia, periksa harganya, lalu lanjutkan pengisian data dan pembayaran melalui Saweria.',
        ],
        'voucher' => [
            'primary' => 'voucher {name}',
            'secondary' => [
                'beli voucher {name}',
                'harga voucher {name}',
                'cara redeem voucher {name}',
            ],
            'title' => 'Voucher {name} | LevelUp Market',
            'description' => 'Lihat pilihan voucher {name} yang tersedia di LevelUp Market dan lanjutkan checkout melalui Saweria.',
            'intro' => 'Pilih voucher {name} yang tersedia dan baca petunjuk produk sebelum melanjutkan checkout melalui Saweria.',
        ],
        'entertainment' => [
            'primary' => 'produk digital {name}',
            'secondary' => [
                'voucher {name}',
                'harga produk {name}',
                'cara beli {name}',
            ],
            'title' => '{name} — Produk Digital | LevelUp Market',
            'description' => 'Lihat pilihan produk digital {name} yang tersedia di LevelUp Market dan lanjutkan checkout melalui Saweria.',
            'intro' => 'Periksa pilihan produk digital {name}, harga, dan petunjuk yang tersedia sebelum melanjutkan checkout melalui Saweria.',
        ],
        'default' => [
            'primary' => '{name}',
            'secondary' => [
                'harga {name}',
                'pilihan {name}',
                'cara beli {name}',
            ],
            'title' => '{name} | LevelUp Market',
            'description' => 'Lihat pilihan {name} yang tersedia di katalog LevelUp Market dan lanjutkan checkout melalui Saweria.',
            'intro' => 'Periksa pilihan {name}, harga, dan petunjuk yang tersedia sebelum melanjutkan checkout melalui Saweria.',
        ],
    ],

    'products' => [
        'mobile-legends-bang-bang' => [
            'primary' => 'top up Mobile Legends',
            'secondary' => [
                'top up ML',
                'top up MLBB',
                'diamond Mobile Legends',
                'diamond MLBB',
                'harga diamond ML',
                'cara top up Mobile Legends',
            ],
            'title' => 'Top Up Mobile Legends (MLBB) | LevelUp Market',
            'description' => 'Lihat paket dan harga Diamond Mobile Legends: Bang Bang yang tersedia, lalu lanjutkan pengisian User ID dan Zone ID melalui Saweria.',
            'intro' => 'Pilih paket Diamond Mobile Legends atau MLBB yang tersedia. Periksa nominal, User ID, dan Zone ID sebelum menyelesaikan checkout di Saweria.',
        ],
        'free-fire' => [
            'primary' => 'top up Free Fire',
            'secondary' => [
                'top up FF',
                'diamond Free Fire',
                'diamond FF',
                'harga diamond FF',
                'cara top up Free Fire',
            ],
            'title' => 'Top Up Free Fire (FF) | LevelUp Market',
            'description' => 'Lihat paket dan harga Diamond Free Fire yang tersedia di LevelUp Market, lalu lanjutkan pengisian Player ID melalui Saweria.',
            'intro' => 'Pilih paket Diamond Free Fire atau FF yang tersedia dan periksa kembali Player ID sebelum melanjutkan checkout di Saweria.',
        ],
        'pubg-mobile' => [
            'primary' => 'top up PUBG Mobile',
            'secondary' => [
                'UC PUBG Mobile',
                'beli UC PUBG Mobile',
                'harga UC PUBG',
                'top up UC PUBG',
                'cara top up PUBG Mobile',
            ],
            'title' => 'Top Up PUBG Mobile UC | LevelUp Market',
            'description' => 'Lihat paket dan harga UC PUBG Mobile yang tersedia di LevelUp Market, lalu lanjutkan pengisian Player ID melalui Saweria.',
            'intro' => 'Pilih paket UC PUBG Mobile yang tersedia dan periksa kembali Player ID serta nominal sebelum melanjutkan checkout di Saweria.',
        ],
        'folaplay' => [
            'primary' => 'voucher FolaPlay',
            'secondary' => [
                'beli voucher FolaPlay',
                'paket FolaPlay',
                'langganan FolaPlay',
                'harga voucher FolaPlay',
                'cara redeem voucher FolaPlay',
            ],
            'title' => 'Voucher dan Paket FolaPlay | LevelUp Market',
            'description' => 'Lihat voucher atau paket FolaPlay yang sedang tersedia di LevelUp Market dan lanjutkan checkout melalui Saweria.',
            'intro' => 'Pilih voucher atau paket FolaPlay yang tersedia. Ketersediaan dan masa akses mengikuti rincian produk yang tampil saat checkout di Saweria.',
        ],
        'valorant' => [
            'primary' => 'top up VALORANT',
            'secondary' => [
                'VALORANT Points',
                'VP VALORANT',
                'beli VALORANT Points',
                'harga VP VALORANT',
                'cara top up VALORANT',
            ],
            'title' => 'Top Up VALORANT Points (VP) | LevelUp Market',
            'description' => 'Lihat paket dan harga VALORANT Points atau VP yang tersedia, lalu lanjutkan pengisian Riot ID melalui Saweria.',
            'intro' => 'Pilih paket VALORANT Points atau VP yang tersedia dan periksa kembali Riot ID sebelum melanjutkan checkout di Saweria.',
        ],
        'magic-chess-go-go' => [
            'primary' => 'top up Magic Chess Go Go',
            'secondary' => [
                'diamond Magic Chess Go Go',
                'top up MCGG',
                'diamond MCGG',
                'harga diamond Magic Chess',
                'cara top up Magic Chess Go Go',
            ],
            'title' => 'Top Up Magic Chess: Go Go Diamond | LevelUp Market',
            'description' => 'Lihat paket dan harga Diamond Magic Chess: Go Go yang tersedia, lalu lanjutkan pengisian User ID dan Zone ID melalui Saweria.',
            'intro' => 'Pilih paket Diamond Magic Chess: Go Go atau MCGG yang tersedia. Periksa User ID, Zone ID, dan nominal sebelum checkout di Saweria.',
        ],
        'delta-force-garena' => [
            'primary' => 'top up Delta Force Garena',
            'secondary' => [
                'Delta Coins Garena',
                'top up Delta Coins',
                'harga Delta Coins',
                'Delta Force Indonesia',
                'cara top up Delta Force',
            ],
            'title' => 'Top Up Delta Force Garena | LevelUp Market',
            'description' => 'Lihat paket top up Delta Force Garena yang tersedia di LevelUp Market dan lanjutkan pengisian data melalui Saweria.',
            'intro' => 'Pilih paket Delta Force Garena yang tersedia. Periksa jenis paket dan data akun yang diminta sebelum melanjutkan checkout di Saweria.',
        ],
        'roblox' => [
            'primary' => 'voucher Roblox',
            'secondary' => [
                'Roblox Gift Card',
                'beli gift card Roblox',
                'Roblox Credit',
                'cara redeem voucher Roblox',
                'cara beli Robux dengan gift card',
            ],
            'title' => 'Voucher dan Gift Card Roblox | LevelUp Market',
            'description' => 'Lihat pilihan Roblox Gift Card yang tersedia. Kode ditukarkan menjadi Roblox Credit atau manfaat sesuai jenis kartu dan ketentuan Roblox.',
            'intro' => 'Pilih voucher atau gift card Roblox yang tersedia. Hasil penukaran mengikuti jenis kode dan ketentuan Roblox.',
        ],
        'steam-voucher-indonesia' => [
            'primary' => 'voucher Steam Indonesia',
            'secondary' => [
                'Steam Wallet Indonesia',
                'kode Steam Wallet',
                'gift card Steam',
                'harga voucher Steam',
                'cara redeem Steam Wallet',
            ],
            'title' => 'Voucher Steam Wallet Indonesia | LevelUp Market',
            'description' => 'Lihat pilihan kode Steam Wallet Indonesia yang tersedia untuk menambah saldo akun Steam setelah kode berhasil ditukarkan.',
            'intro' => 'Pilih kode Steam Wallet Indonesia yang tersedia dan pastikan wilayah serta mata uang akun sesuai sebelum menukarkan kode di Steam.',
        ],
        'google-play-indonesia' => [
            'primary' => 'voucher Google Play Indonesia',
            'secondary' => [
                'kode voucher Google Play',
                'saldo Google Play',
                'gift card Google Play Indonesia',
                'harga voucher Google Play',
                'cara redeem voucher Google Play',
            ],
            'title' => 'Voucher Google Play Indonesia | LevelUp Market',
            'description' => 'Lihat pilihan kode voucher Google Play Indonesia yang tersedia untuk menambah Saldo Google Play setelah kode berhasil ditukarkan.',
            'intro' => 'Pilih kode voucher Google Play Indonesia yang tersedia. Pastikan negara akun Google Play sesuai sebelum membeli dan menukarkan kode.',
        ],
    ],
];

<?php

/*
 * Template tautan pendek.
 *
 * Tiap template hanya mengisi nilai awal pada formulir; seluruhnya masih bisa
 * diubah pengelola sebelum disimpan. Gunanya supaya penamaan UTM konsisten --
 * "ig" hari ini dan "instagram" besok akan terbaca sebagai dua sumber berbeda
 * di laporan, dan itu yang paling sering merusak pembacaan data.
 */
return [
    'templates' => [
        'instagram_bio' => [
            'label' => 'Instagram Bio',
            'icon' => '📷',
            'description' => 'Tautan tetap di bio profil Instagram.',
            'utm_source' => 'instagram',
            'utm_medium' => 'bio',
            'utm_campaign' => 'profil',
        ],
        'instagram_story' => [
            'label' => 'Instagram Story',
            'icon' => '⚡',
            'description' => 'Stiker tautan pada story. Cocok untuk promo jangka pendek.',
            'utm_source' => 'instagram',
            'utm_medium' => 'story',
            'utm_campaign' => 'promo',
        ],
        'instagram_post' => [
            'label' => 'Instagram Post',
            'icon' => '🖼️',
            'description' => 'Tautan yang disebut pada caption unggahan.',
            'utm_source' => 'instagram',
            'utm_medium' => 'post',
            'utm_campaign' => 'konten',
        ],
        'tiktok' => [
            'label' => 'TikTok',
            'icon' => '🎵',
            'description' => 'Bio atau komentar tersemat di TikTok.',
            'utm_source' => 'tiktok',
            'utm_medium' => 'bio',
            'utm_campaign' => 'profil',
        ],
        'whatsapp' => [
            'label' => 'Bagikan ke Teman',
            'icon' => '💬',
            'description' => 'Dikirim lewat WhatsApp atau pesan pribadi.',
            'utm_source' => 'whatsapp',
            'utm_medium' => 'pesan',
            'utm_campaign' => 'berbagi',
        ],
        'article' => [
            'label' => 'Artikel / Blog',
            'icon' => '📰',
            'description' => 'Disisipkan pada tulisan atau situs lain.',
            'utm_source' => 'artikel',
            'utm_medium' => 'konten',
            'utm_campaign' => 'seo',
        ],
        'affiliate' => [
            'label' => 'Afiliasi / Mitra',
            'icon' => '🤝',
            'description' => 'Diberikan kepada mitra. Isi Konten dengan nama mitranya.',
            'utm_source' => 'afiliasi',
            'utm_medium' => 'mitra',
            'utm_campaign' => 'kerjasama',
            'utm_content' => 'nama-mitra',
        ],
        'youtube' => [
            'label' => 'YouTube',
            'icon' => '▶️',
            'description' => 'Deskripsi video atau komentar tersemat.',
            'utm_source' => 'youtube',
            'utm_medium' => 'deskripsi',
            'utm_campaign' => 'video',
        ],
        'discord' => [
            'label' => 'Discord / Komunitas',
            'icon' => '🎮',
            'description' => 'Dibagikan di server komunitas game.',
            'utm_source' => 'discord',
            'utm_medium' => 'komunitas',
            'utm_campaign' => 'server',
        ],
        'ads' => [
            'label' => 'Iklan Berbayar',
            'icon' => '💰',
            'description' => 'Untuk iklan berbayar. Isi Konten dengan varian materinya.',
            'utm_source' => 'meta',
            'utm_medium' => 'cpc',
            'utm_campaign' => 'iklan',
            'utm_content' => 'varian-a',
        ],
        'custom' => [
            'label' => 'Bebas',
            'icon' => '⚙️',
            'description' => 'Isi semua nilainya sendiri.',
        ],
    ],
];

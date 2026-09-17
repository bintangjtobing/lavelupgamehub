<?php

return [
    /*
     * Berkas kunci service account (JSON) yang diunduh dari Google Cloud.
     *
     * Simpan DI LUAR direktori public. Di server, tempat yang tepat adalah
     * folder shared supaya tidak ikut terhapus saat rilis baru dipasang:
     *   /var/www/levelupgamehub/shared/google-service-account.json
     */
    'credentials' => env('GOOGLE_CREDENTIALS_PATH'),

    'ga4' => [
        // ID numerik properti GA4, bukan G-XXXXXXX. Lihat Admin > Property details.
        'property_id' => env('GA4_PROPERTY_ID'),

        // Dipakai mengirim event purchase dari server lewat Measurement Protocol
        'api_secret' => env('GA4_API_SECRET'),
        'measurement_id' => env('GA4_MEASUREMENT_ID', 'G-ZMQ2EZKSBP'),
    ],

    'search_console' => [
        /*
         * Situs diverifikasi lewat DNS TXT, sehingga properti di Search Console
         * berjenis Domain. Penulisannya memakai awalan "sc-domain:", bukan URL.
         */
        'site_url' => env('GSC_SITE_URL', 'sc-domain:levelupgamehub.com'),
    ],

    /*
     * Laporan Google berubah lambat dan permintaannya berkuota, jadi hasilnya
     * disimpan sementara. Panel tetap terbuka cepat walau API sedang lambat.
     */
    'cache_minutes' => (int) env('GOOGLE_CACHE_MINUTES', 30),
];

<?php

return [
    // The Google tag is the sole GA4 loader. Do not also load the old GTM container.
    'measurement_id' => env('GA4_MEASUREMENT_ID', 'G-ZMQ2EZKSBP'),
    'enabled' => (bool) env('ANALYTICS_ENABLED', env('APP_ENV') === 'production'),
    'debug' => (bool) env('ANALYTICS_DEBUG', false),
    'production_hosts' => ['levelupgamehub.com', 'www.levelupgamehub.com'],
    'meta_pixel_id' => env('META_PIXEL_ID', '1632311901029140'),
    'clarity_id' => env('CLARITY_PROJECT_ID', 'oturnn0i9y'),
];

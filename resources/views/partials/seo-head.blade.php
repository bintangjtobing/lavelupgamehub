@php
    $seo ??= app(\App\Support\Seo::class)->forRequest(
        request(),
        isset($item) && $item instanceof \App\Models\CatalogItem ? $item : null
    );
@endphp
<title>{{ $seo['title'] }}</title>
<meta name="description" content="{{ $seo['description'] }}">
<meta name="keywords" content="{{ $seo['keywords'] }}">
<meta name="author" content="{{ $seo['site_name'] }}">
<meta name="publisher" content="{{ $seo['site_name'] }}">
<meta name="robots" content="{{ request()->isMethod('post') ? 'noindex, nofollow, noarchive' : 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1' }}">
<meta name="theme-color" content="#242b36">
<meta name="format-detection" content="telephone=no">
<link rel="canonical" href="{{ $seo['canonical'] }}">

<meta property="og:locale" content="{{ $seo['locale'] }}">
<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ $seo['site_name'] }}">
<meta property="og:title" content="{{ $seo['title'] }}">
<meta property="og:description" content="{{ $seo['description'] }}">
<meta property="og:url" content="{{ $seo['canonical'] }}">
<meta property="og:image" content="{{ $seo['image'] }}">
<meta property="og:image:secure_url" content="{{ $seo['image'] }}">
<meta property="og:image:type" content="image/png">
<meta property="og:image:width" content="1640">
<meta property="og:image:height" content="924">
<meta property="og:image:alt" content="{{ $seo['image_alt'] }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seo['title'] }}">
<meta name="twitter:description" content="{{ $seo['description'] }}">
<meta name="twitter:image" content="{{ $seo['image'] }}">
<meta name="twitter:image:alt" content="{{ $seo['image_alt'] }}">

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="icon" href="/favicon-32x32.png" type="image/png" sizes="32x32">
<link rel="icon" href="/favicon-96x96.png" type="image/png" sizes="96x96">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">
<link rel="manifest" href="/site.webmanifest">

@foreach ($seo['structured_data'] as $structuredData)
<script type="application/ld+json">{!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
@endforeach

@extends('welcome')
@section('title', 'Top Up ' . $item->name)

@push('css')
    <link rel="stylesheet" href="{{ asset('frontend/css/product-detail.css') }}">
@endpush

@section('content')
<main class="lu-product" data-analytics-product
    data-analytics-item="{{ json_encode(['item_id' => $item->slug, 'item_name' => $item->name, 'item_brand' => $item->publisher, 'item_category' => $item->category_label]) }}">
    <div class="container">
        <nav class="lu-product-crumb" aria-label="Breadcrumb"><a href="/topup" title="Kembali ke katalog game dan voucher">Semua game &amp; voucher</a><span aria-hidden="true">/</span><span>{{ $item->name }}</span></nav>
        <header class="lu-product-hero">
            <img src="{{ $item->image_url }}" alt="Cover {{ $item->name }}" title="{{ $item->name }} — {{ $item->publisher ?: 'Katalog LevelUp Market' }}" width="144" height="144" fetchpriority="high" decoding="async">
            <div>
                <p class="lu-product-eyebrow">{{ $item->publisher ?: $item->category_label }}</p>
                <h1>Top Up {{ $item->name }}</h1>
                <p>{{ app(\App\Support\Seo::class)->productCopy($item)['intro'] }}</p>
                @if ($description)<p>{{ $description }}</p>@endif
                <span class="lu-product-source">Pembayaran melalui Saweria</span>
            </div>
        </header>
        @if ($unavailable)
            <section class="lu-product-panel lu-product-unavailable" aria-labelledby="unavailable-title">
                <h2 id="unavailable-title">Harga belum bisa dimuat</h2>
                <p>Daftar harga Saweria sedang tidak tersedia. Coba muat ulang halaman atau lihat pilihan paket langsung di Saweria.</p>
                <div class="lu-product-actions"><a class="lu-product-primary" href="{{ $item->game_url }}" title="Ambil ulang harga {{ $item->name }}">Muat ulang harga</a><a href="{{ $item->topup_url }}" title="Lihat {{ $item->name }} di Saweria" rel="noopener noreferrer">Buka toko Saweria &rarr;</a></div>
            </section>
        @elseif (empty($products))
            <section class="lu-product-panel"><h2>Belum ada paket tersedia</h2><p>Paket untuk produk ini sedang tidak tersedia. Silakan cek kembali nanti.</p><a href="/topup" title="Jelajahi semua game dan voucher">Jelajahi game lainnya &rarr;</a></section>
        @else
            <div class="lu-product-layout">
                <section class="lu-product-panel" aria-labelledby="packages-title">
                    <div class="lu-product-heading"><span class="lu-product-step">1</span><div><h2 id="packages-title">Pilih nominal</h2><p>Harga diambil dari Saweria saat halaman dibuka.</p></div></div>
                    <noscript><p>Pilih nama paket untuk melanjutkan ke Saweria.</p></noscript>
                    @foreach (collect($products)->groupBy('category') as $category => $options)
                        <fieldset class="lu-product-group">
                            <legend>{{ $category ?: 'Pilihan paket' }}</legend>
                            <div class="lu-product-options">
                                @foreach ($options as $product)
                                    <a class="lu-product-option" href="{{ $product['checkout_url'] }}" title="Pilih {{ $product['name'] }} untuk {{ $item->name }}" data-product-id="{{ $product['id'] }}" data-product-category="{{ $product['category'] }}" data-product-name="{{ $product['name'] }}" data-product-price="{{ $product['price'] }}" rel="noopener noreferrer">
                                        <span>{{ $product['name'] }}</span>
                                        <strong>Rp {{ number_format($product['price'], 0, ',', '.') }}</strong>
                                    </a>
                                @endforeach
                            </div>
                        </fieldset>
                    @endforeach
                </section>
                <aside class="lu-product-sidebar">
                    <section class="lu-product-panel lu-product-summary" aria-labelledby="payment-title">
                        <div class="lu-product-heading"><span class="lu-product-step">2</span><h2 id="payment-title">Lanjut ke pembayaran</h2></div>
                        <div aria-live="polite" aria-atomic="true"><p id="selected-name">Pilih nominal terlebih dahulu</p><strong id="selected-price" class="lu-product-total">—</strong></div>
                        <p class="lu-product-fee">Harga paket belum termasuk biaya pembayaran yang mungkin berlaku. Total akhir ditampilkan di Saweria.</p>
                        <a id="saweria-checkout" class="lu-product-primary" title="Lengkapi data dan pembayaran di Saweria" role="link" aria-disabled="true" tabindex="-1">Lanjut ke Saweria <span aria-hidden="true">&rarr;</span></a>
                        <p class="lu-product-handoff">{{ $item->variant === 'DIGITAL' ? 'Isi ID akun game dan pilih metode pembayaran di Saweria.' : 'Lengkapi data penerima dan pilih metode pembayaran di Saweria.' }}</p>
                    </section>
                    @if ($instructions || count($fields))
                        <section class="lu-product-panel lu-product-guide"><h2>Siapkan data akunmu</h2>
                            @if (count($fields))<p>Data yang perlu diisi di Saweria:</p><ul>@foreach ($fields as $field)<li>{{ $field['label'] ?? $field['key'] ?? 'ID akun' }}</li>@endforeach</ul>@endif
                            @if ($instructions)<p>{{ $instructions }}</p>@endif
                        </section>
                    @endif
                </aside>
            </div>
            <div class="lu-product-mobile-checkout" id="mobile-checkout" hidden>
                <div><span id="mobile-selected-name"></span><strong id="mobile-selected-price"></strong><small>Biaya pembayaran dihitung di Saweria</small></div>
                <a class="lu-product-primary" id="mobile-saweria-checkout" title="Lengkapi data dan pembayaran di Saweria">Lanjut ke Saweria &rarr;</a>
            </div>
        @endif
        @if (count($faqs))
            <section class="lu-product-faq" aria-labelledby="product-faq-title"><h2 id="product-faq-title">Tentang {{ $item->name }}</h2>
                @foreach ($faqs as $faq)<details><summary>{{ $faq['question'] }}</summary><p>{{ $faq['answer'] }}</p></details>@endforeach
            </section>
        @endif
    </div>
</main>
@endsection

@push('script')
<script src="{{ asset('frontend/js/product-detail.js') }}" defer></script>
@endpush

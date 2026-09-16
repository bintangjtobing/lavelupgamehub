@extends('welcome')
@section('title', 'Katalog Top Up Game dan Voucher Digital')

@push('css')
@endpush

@section('content')
    <section class="lu-section pt-80">
        <div class="container">
            <div class="lu-intro">
                <h1>Katalog Top Up Game dan Voucher Digital</h1>
                <p>
                    Jelajahi <strong>{{ $catalogTotal }} produk yang sedang aktif</strong> di katalog LevelUp Gaming
                    Market. Gunakan kategori atau kolom pencarian untuk menemukan produk, lalu buka detailnya untuk
                    melihat pilihan paket dan harga terbaru. Pengisian data serta pembayaran dilakukan di Saweria.
                </p>
            </div>
        </div>
    </section>

    @include('frontend.partials.best-sellers')

    @include('frontend.partials.catalog-browser')
@endsection

@push('script')
@endpush

@extends('welcome')

@section('title', 'Tentang LevelUp Market')

@push('css')
@endpush

@section('content')
<section class="lu-section pt-80" aria-labelledby="about-page-title">
    <div class="container">
        <div class="lu-intro">
            <h1 id="about-page-title">Tentang LevelUp Market</h1>
            <p>LevelUp Market membantu pengunjung menemukan game dan produk digital dalam satu katalog.
                Informasi produk disajikan di situs ini, sedangkan pemilihan data akun dan pembayaran dilanjutkan
                melalui halaman checkout Saweria.</p>
        </div>
    </div>
</section>
@include('frontend.components.about-company')
@include('frontend.components.why-choose')
@include('frontend.components.testimonial')
@endsection


@push('script')
@endpush

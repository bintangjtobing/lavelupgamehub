@extends('welcome')
@section('title', 'Top Up Game dan Voucher Digital')
@push('css')
@endpush
@section('content')
@include('frontend.partials.banner-slider')

@include('frontend.partials.seo-intro')

@include('frontend.partials.services')

@include('frontend.partials.donation-section')

@include('frontend.partials.best-sellers')

@include('frontend.partials.catalog-browser')

@include('frontend.partials.game-stats-section')

@include('frontend.components.about-company')

@include('frontend.components.why-choose')

@include('frontend.components.testimonial')
@endsection

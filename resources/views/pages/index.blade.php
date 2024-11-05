@extends('welcome')
@section('title','Homepage')
@push('css')
@endpush
@section('content')
@include('frontend.partials.banner-slider')

@include('frontend.partials.services')

@include('frontend.partials.donation-section')

@include('frontend.partials.top-up')

@include('frontend.components.about-company')

@include('frontend.components.why-choose')

@include('frontend.components.testimonial')
@endsection

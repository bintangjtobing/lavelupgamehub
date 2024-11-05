@extends('welcome')
@section('title', 'Contact us')

@push('css')
@endpush

@section('content')
<section class="contact-section ptb-120">
    <div class="container">
        <div class="row justify-content-center mb-30-none">
            <div class="col-xl-5 col-lg-5 mb-30">
                <div class="contact-widget">
                    <div class="contact-form-header">
                        <h2 class="title">Hubungi Kami</h2>
                        <p>Punya pertanyaan atau butuh bantuan untuk meningkatkan pengalamanmu? Kirim pesan melalui form
                            di bawah, dan tim kami akan segera menghubungimu!</p>
                    </div>
                    <ul class="contact-item-list">
                        <li>
                            <a href="#0">
                                <div class="contact-item-icon">
                                    <i class="las la-map-marked-alt"></i>
                                </div>
                                <div class="contact-item-content">
                                    <h5 class="title">Lokasi Kami</h5>
                                    <span class="sub-title">Jl. Taman Palem Lestari Blk. C15 No.37-38, RT.9/RW.13,
                                        Pegadungan, Kec. Kalideres, Kota Jakarta Barat, Daerah Khusus Ibukota Jakarta
                                        11830</span>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="#0">
                                <div class="contact-item-icon tow">
                                    <i class="las la-phone-volume"></i>
                                </div>
                                <div class="contact-item-content">
                                    <h5 class="title">Telepon kami</h5>
                                    <span class="sub-title">Jam kerja kami adalah Senin – Jumat, 9 pagi - 6 sore</span>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="#0">
                                <div class="contact-item-icon three">
                                    <i class="las la-envelope"></i>
                                </div>
                                <div class="contact-item-content">
                                    <h5 class="title">Email langsung</h5>
                                    <span class="sub-title">cs@levelupgamehub.com</span>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-xl-7 col-lg-7 mb-30">
                <div class="contact-form-inner wow fadeInRight" data-wow-duration="1s" data-wow-delay=".4s">
                    <div class="contact-form-area">
                        <form class="contact-form" method="POST" action="/message">
                            @csrf
                            <div class="row justify-content-center mb-10-none">
                                <div class="col-lg-12 form-group">
                                    <label>Nama Anda <span class="text--base">*</span></label>
                                    <input type="text" name="name" class="form--control" placeholder="Masukkan nama">
                                </div>
                                <div class="col-lg-12 form-group">
                                    <label>Email Anda <span class="text--base">*</span></label>
                                    <input type="email" name="email" class="form--control" placeholder="Masukkan email">
                                </div>
                                <div class="col-lg-12 form-group">
                                    <label>Pesan <span class="text--base">*</span></label>
                                    <textarea class="form--control" name="message" placeholder="Pesan Anda"></textarea>
                                </div>
                                <div class="col-lg-12 form-group">
                                    <button type="submit" class="btn--base mt-10 contact-btn">Kirim Pesan <i
                                            class="las la-angle-right"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@push('script')
@endpush

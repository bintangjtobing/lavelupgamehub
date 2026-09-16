@extends('welcome')
@section('title', 'Hubungi LevelUp Market')

@section('content')
<section class="contact-section ptb-120" aria-labelledby="contact-title">
    <div class="container">
        <div class="row justify-content-center mb-30-none">
            <div class="col-xl-5 col-lg-5 mb-30">
                <div class="contact-widget">
                    <div class="contact-form-header">
                        <h1 class="title" id="contact-title">Hubungi LevelUp Market</h1>
                        <p>Butuh bantuan menggunakan katalog atau ingin menanyakan produk? Kirim pesan melalui
                            formulir, email, atau WhatsApp. Sertakan nama produk dan jelaskan kendalanya agar pertanyaan
                            lebih mudah dipahami.</p>
                    </div>
                    <ul class="contact-item-list">
                        <li>
                            <a href="https://wa.me/6285195922910" title="Hubungi LevelUp Market melalui WhatsApp"
                                rel="noopener noreferrer">
                                <div class="contact-item-icon"><i class="lab la-whatsapp" aria-hidden="true"></i></div>
                                <div class="contact-item-content">
                                    <h2 class="title">WhatsApp</h2>
                                    <span class="sub-title">+62 851-9592-2910</span>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="mailto:help@levelupgamehub.com"
                                title="Kirim email ke bantuan LevelUp Market">
                                <div class="contact-item-icon three"><i class="las la-envelope" aria-hidden="true"></i></div>
                                <div class="contact-item-content">
                                    <h2 class="title">Email</h2>
                                    <span class="sub-title">help@levelupgamehub.com</span>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-xl-7 col-lg-7 mb-30">
                <div class="contact-form-inner wow fadeInRight" data-wow-duration="1s" data-wow-delay=".4s">
                    <div class="contact-form-area">
                        <h2>Kirim Pesan</h2>
                        <p>Data yang diisi di sini digunakan untuk menerima dan menjawab pertanyaanmu.</p>
                        <form class="contact-form" method="POST" action="/message">
                            @csrf
                            <div class="row justify-content-center mb-10-none">
                                <div class="col-lg-12 form-group">
                                    <label for="contact-name">Nama <span class="text--base">*</span></label>
                                    <input id="contact-name" type="text" name="name" class="form--control"
                                        value="{{ old('name') }}" placeholder="Masukkan nama" required maxlength="255"
                                        autocomplete="name">
                                </div>
                                <div class="col-lg-12 form-group">
                                    <label for="contact-email">Email <span class="text--base">*</span></label>
                                    <input id="contact-email" type="email" name="email" class="form--control"
                                        value="{{ old('email') }}" placeholder="Masukkan email" required autocomplete="email">
                                </div>
                                <div class="col-lg-12 form-group">
                                    <label for="contact-message">Pesan <span class="text--base">*</span></label>
                                    <textarea id="contact-message" class="form--control" name="message"
                                        placeholder="Tuliskan pertanyaan atau kendala" required>{{ old('message') }}</textarea>
                                </div>
                                <div class="col-lg-12 form-group">
                                    <button type="submit" class="btn--base mt-10 contact-btn">Kirim Pesan <i
                                            class="las la-angle-right" aria-hidden="true"></i></button>
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

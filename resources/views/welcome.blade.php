<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('partials.seo-head')
    @include('partials.analytics-head')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-lazyload/17.6.1/lazyload.min.js"></script>

    <link rel="stylesheet" href="{{ asset('frontend/css/line-awesome.css')}}">
    <link rel="stylesheet" href="{{ asset('frontend/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/swiper.css')}}">
    <link rel="stylesheet" href="{{ asset('frontend/css/animate.css')}}">
    <link rel="stylesheet" href="{{ asset('frontend/css/odometer.css')}}">
    <link rel="stylesheet" href="{{ asset('frontend/css/nice-select.css')}}">
    <link rel="stylesheet" href="{{ asset('frontend/css/lightcase.css')}}">
    <link rel="stylesheet" href="{{ asset('frontend/fileholder-style.css')}}" type="text/css">
    <link rel="stylesheet" href="{{ asset('frontend/css/style.css')}}">
    <link rel="stylesheet" href="{{ asset('frontend/css/levelup-custom.css')}}">
    <style>
        :root {
            --base_color: #9CFF1E;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #ffffff;
        }
    </style>

    <link
        href="https://fonts.googleapis.com/css2?family=Oxanium:wght@200;300;400;500;600;700;800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <!-- fontawesome css link -->
    <link rel="stylesheet" href="{{ asset('backend/css/fontawesome-all.css') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    @stack('css')
</head>

<body>
    <div class="preloader">
        <div class="loader-inner">
            <div class="loader-circle">
                <img src="https://res.cloudinary.com/boxityapp/image/upload/v1730820799/levelupgaming/donation/ccterurxh9rxeaglqikl.gif"
                    alt="" title="Animasi pemuatan LevelUp Market" aria-hidden="true">
            </div>
            <div class="loader-line-mask">
                <div class="loader-line"></div>
            </div>
        </div>
    </div>
    <div id="body-overlay" class="body-overlay"></div>

    <header class="header-section home">
        <div class="header">
            <div class="header-bottom-area">
                <div class="container-fluid">
                    <div class="header-menu-content">
                        <div class="logo-wrapper">
                            <a class="site-logo site-title" href="/" title="Beranda LevelUp Market">
                                <img src="{{ asset('images/brand/logo.png') }}"
                                    width="425" height="150" alt="LevelUp Market" title="LevelUp Market — top up game dan voucher digital">
                            </a>
                            <button class="logo-btn" aria-label="Buka menu navigasi"><i class="las la-bars" aria-hidden="true"></i></button>
                        </div>
                        <div class="header-search">
                            <div class="header-search-area">
                                <input type="search" class="top-up-search" id="game-search"
                                    placeholder="Cari game kesukaan kamu" aria-label="Cari game atau voucher">
                                <span><i class="las la-search"></i></span>
                            </div>
                            <div class="header-mobile-search-area">
                                <a href="#0" class="header-mobile-search-btn" title="Buka pencarian game">
                                    <i class="las la-search"></i>
                                </a>
                                <div class="header-mobile-search-form-area">
                                    <input type="search" placeholder="Cari game kesukaan kamu" id="game-search-mobile" aria-label="Cari game atau voucher">
                                    <span><i class="las la-search"></i></span>
                                </div>
                            </div>
                            <ul class="header-search-result" id="search-results"></ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- Tempat halaman menitipkan baris berjalan selebar layar, tepat di bawah
         menu navigasi. Halaman yang tidak mengisinya tidak terpengaruh. --}}
    @stack('subheader')

    <div class="main-side-menu">
        <div class="main-side-menu-logo-area">
            <div class="thumb-logo">
                <img src="{{ asset('images/brand/logo.png') }}"
                    width="425" height="150" alt="LevelUp Market" title="Logo LevelUp Market">
            </div>
            <span class="main-side-menu-cross"><i class="las la-times"></i></span>
        </div>
        <ul class="main-side-menu-list">
            <li>
                <a href="/" class="active" title="Beranda LevelUp Market">
                    <div class="main-side-menu-item">
                        <i class="las la-th-large"></i> Beranda
                    </div>
                    <span><i class="las la-angle-right"></i></span>
                </a>
            </li>

            <li>
                <a href="/topup" class="" title="Lihat semua game dan voucher">
                    <div class="main-side-menu-item">
                        <i class="las la-coins"></i> Top Up
                    </div>
                    <span><i class="las la-angle-right"></i></span>
                </a>
            </li>
            <li>
                <a href="/track-order" title="Lacak status pesanan dengan Track ID Saweria">
                    <div class="main-side-menu-item"><i class="las la-receipt" aria-hidden="true"></i> Lacak Pesanan</div>
                    <span><i class="las la-angle-right" aria-hidden="true"></i></span>
                </a>
            </li>
            <li>
                <a href="/about" class="" title="Tentang LevelUp Market">
                    <div class="main-side-menu-item">
                        <i class="las la-info-circle"></i> Tentang Kami
                    </div>
                    <span><i class="las la-angle-right"></i></span>
                </a>
            </li>
            <li>
                <a href="/faq" class="" title="Panduan dan pertanyaan top up">
                    <div class="main-side-menu-item">
                        <i class="las la-question-circle"></i> FAQ
                    </div>
                    <span><i class="las la-angle-right"></i></span>
                </a>
            </li>
            <li>
                <a href="/contact" class="" title="Hubungi LevelUp Market">
                    <div class="main-side-menu-item">
                        <i class="las la-headset"></i> Kontak
                    </div>
                    <span><i class="las la-angle-right"></i></span>
                </a>
            </li>
        </ul>
    </div>

    @yield('content')


    <a href="#" class="scrollToTop" title="Kembali ke atas">
        <i class="las la-angle-up"></i>
        <small>Top</small>
    </a>
    <footer class="footer-section pt-60 bg_img" data-background="{{ asset('frontend/images/element/bg1.jpg') }}">
        <div class="container">
            <div class="footer-wrapper">
                <div class="row mb-30-none">
                    <!-- Logo dan Deskripsi -->
                    <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-sm-6 mb-30">
                        <div class="footer-widget">
                            <div class="footer-logo">
                                <a href="/" class="site-logo site-title" title="Beranda LevelUp Market">
                                    <img src="{{ asset('images/brand/logo.png') }}"
                                        width="425" height="150" alt="LevelUp Market" title="Logo LevelUp Market">
                                </a>
                            </div>
                            <div class="footer-content">
                                <p>LevelUp Market membantu kamu memilih top up game dan voucher digital.
                                    Lihat pilihan nominal dan harga terkini, lalu selesaikan pembayaran melalui Saweria.</p>
                            </div>
                            <div class="footer-content-bottom">
                                <ul class="footer-list logo">
                                    <li><a href="mailto:help@levelupgamehub.com" title="Kirim email ke LevelUp Market"><i class="las la-envelope me-1"></i>
                                            help@levelupgamehub.com</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Link Bermanfaat -->
                    <div class="col-xxl-2 col-xl-2 col-lg-2 col-md-6 col-sm-6 mb-30">
                        <div class="footer-widget">
                            <h4 class="widget-title">Link Bermanfaat</h4>
                            <ul class="footer-list">
                                <li><a href="/about" title="Tentang LevelUp Market">Tentang Kami</a></li>
                                <li><a href="/track-order" title="Lacak pembayaran dan pengiriman pesanan">Lacak Pesanan</a></li>
                                <li><a href="/privacy-policy" title="Baca kebijakan privasi">Kebijakan Privasi</a></li>
                                <li><a href="/terms-and-conditions" title="Baca syarat dan ketentuan">Syarat dan Ketentuan</a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Newsletter -->
                    <div class="col-xxl-6 col-xl-4 col-lg-4 col-md-6 col-sm-6 mb-30">
                        @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                        @endif
                        <div class="footer-widget">
                            <h4 class="widget-title">Tulis Review</h4>
                            <p style="margin-top:0;">Berikan pendapat Anda tentang layanan kami. Kami menghargai setiap
                                masukan Anda!</p>
                            <form action="{{ route('review.store') }}" method="post" id="reviewForm"
                                aria-label="Formulir ulasan">
                                <span id="tulis-ulasan" class="lu-anchor" aria-hidden="true"></span>
                                @csrf
                                <ul class="footer-list two" style="list-style-type:none;">
                                    <li>
                                        <input type="text" name="name" placeholder="Nama" class="form--control"
                                            required>
                                        <span class="input-icon"></span>
                                    </li>
                                    <li class="row">
                                        <div class="col-md-6">
                                            <input type="email" name="email" placeholder="Email (Optional)"
                                                class="form--control">
                                            <span class="input-icon"></span>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" name="phone" placeholder="No Telepon"
                                                class="form--control" required>
                                            <span class="input-icon"></span>
                                        </div>
                                    </li>
                                    <li>
                                        <textarea name="message" placeholder="Tulis Review Anda" class="form--control"
                                            required></textarea>
                                    </li>
                                    <li class="agree-terms-row">
                                        <input type="checkbox" name="agree_terms" id="agreeTerms" value="1">
                                        <label for="agreeTerms">
                                            Saya setuju nama dan isi ulasan saya ditampilkan di situs dan media sosial
                                            LevelUp Market. Email dan nomor telepon tidak ditampilkan pada ulasan publik.
                                        </label>
                                    </li>
                                    <li>
                                        <button type="submit" class="btn--base sub-btn" disabled id="submitBtn">
                                            Kirim Review <i class="las la-arrow-right ms-1"></i>
                                        </button>
                                    </li>
                                </ul>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Hak Cipta dan Media Sosial -->
                <div class="copyright-area">
                    <div class="copyright-wrapper">
                        <p>&copy; Copyright -<span class="text--base">LevelUp Market</span> {{ date('Y') }}.</p>
                        <ul class="footer-social-list">
                            <li>
                                <a
                                    href="https://wa.me/6285195922910?text=Halo%20kak%20LevelUp!%20Saya%20mau%20tanya-tanya%20seputar%20voucher%20game%20dan%20cara%20topup%20disini%20dong" title="Hubungi LevelUp Market melalui WhatsApp"><i
                                        class="lab la-whatsapp"></i> Whatsapp</a>
                            </li>
                            <li>
                                <a href="https://www.facebook.com/profile.php?id=61568151335436" title="LevelUp Market di Facebook"><i
                                        class="lab la-facebook-f"></i> Facebook</a>
                            </li>
                            <li>
                                <a href="https://www.instagram.com/levelupmarketgaming" title="LevelUp Market di Instagram"><i class="lab la-instagram"></i>
                                    Instagram</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="{{ asset('frontend/js/jquery-3.5.1.js')}}"></script>
    <script src="{{ asset('frontend/js/bootstrap.bundle.js')}}"></script>
    <script src="{{ asset('frontend/js/swiper.js')}}"></script>
    <script src="{{ asset('frontend/js/odometer.js')}}"></script>
    <script src="{{ asset('frontend/js/viewport.jquery.js')}}"></script>
    <script src="{{ asset('frontend/js/smoothscroll.js')}}"></script>
    <script src="{{ asset('frontend/js/jquery.nice-select.js')}}"></script>
    <script src="{{ asset('frontend/js/lightcase.js')}}"></script>
    <script src="{{ asset('backend/js/select2.js')}}"></script>
    <script src="{{ asset('backend/library/popup/jquery.magnific-popup.js')}}"></script>
    <script src="{{ asset('frontend/js/main.js') }}"></script>

    @stack('script')
    <script>
        var lazyLoadInstance = new LazyLoad({
            elements_selector: ".lazy" // Targets elements with the 'lazy' class
        });
    </script>
    <script>
        // Tombol kirim baru aktif setelah persetujuan dicentang
        const agreeTerms = document.getElementById('agreeTerms');
        const submitBtn = document.getElementById('submitBtn');

        if (agreeTerms && submitBtn) {
            agreeTerms.addEventListener('change', function() {
                submitBtn.disabled = !this.checked;
            });
        }
    </script>



    {{-- For Live Search --}}

    <script>
       // Function to handle live search
    let latestSearch = '';
    function liveSearch(query) {
        latestSearch = query;
        if (query.length > 0) {
            // Fetch API for AJAX call
            fetch("{{ route('games.search') }}?q=" + encodeURIComponent(query))
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (query !== latestSearch) return;
                    document.dispatchEvent(new CustomEvent('levelup:search', { detail: { source: 'header', results: data } }));
                    const searchResults = document.getElementById('search-results');
                    searchResults.innerHTML = ''; // Clear previous results

                    if (data.length > 0) {
                        data.forEach(game => {
                            const listItem = document.createElement('li');
                            listItem.style.display = 'flex';
                            listItem.style.alignItems = 'center';
                            listItem.style.gap = '10px';

                            const image = document.createElement('img');
                            image.src = game.image_url;
                            image.alt = game.name;
                            image.title = 'Cover ' + game.name;
                            image.width = 80;
                            image.height = 80;
                            image.style.width = '40px';
                            image.style.height = '40px';
                            image.style.borderRadius = '20%';

                            const link = document.createElement('a');
                            link.href = game.game_url;
                            link.textContent = game.name;
                            link.title = 'Lihat nominal top up ' + game.name;
                            link.dataset.analyticsItem = JSON.stringify({ item_id: game.slug, item_name: game.name, item_category: game.type });
                            link.dataset.analyticsList = 'header_search';

                            listItem.appendChild(image);
                            listItem.appendChild(link);
                            searchResults.appendChild(listItem);
                        });
                    } else {
                        const noResults = document.createElement('li');
                        noResults.textContent = 'Game tidak ditemukan.';
                        searchResults.appendChild(noResults);
                    }
                })
                .catch(error => {
                    const searchResults = document.getElementById('search-results');
                    searchResults.innerHTML = ''; // Clear previous results
                    const errorMessage = document.createElement('li');
                    errorMessage.textContent = 'Pencarian belum tersedia. Silakan coba lagi.';
                    searchResults.appendChild(errorMessage);
                    console.error('Error:', error);
                });
        } else {
            const searchResults = document.getElementById('search-results');
            searchResults.innerHTML = ''; // Clear results if input is empty
        }
    }

    // Attach event listeners for live search
    let searchDelay;
    ['game-search', 'game-search-mobile'].forEach(id => {
        document.getElementById(id).addEventListener('focus', () => {
            document.getElementById('search-results').classList.add('active');
        });
        document.getElementById(id).addEventListener('input', function () {
            latestSearch = this.value;
            clearTimeout(searchDelay);
            const query = this.value;
            searchDelay = setTimeout(() => liveSearch(query), 300);
        });
    });
    </script>

    @stack('scripts')
    <script src="{{ asset('frontend/js/analytics.js') }}" defer></script>
</body>

</html>

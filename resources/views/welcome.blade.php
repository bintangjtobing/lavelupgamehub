<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Pixel Code -->
    <script>
        !function(f,b,e,v,n,t,s)
    {
      if(f.fbq) return; n=f.fbq=function(){n.callMethod ?
      n.callMethod.apply(n,arguments) : n.queue.push(arguments)};
      if(!f._fbq) f._fbq=n; n.push=n; n.loaded=!0; n.version='2.0';
      n.queue=[]; t=b.createElement(e); t.async=!0;
      t.src=v; s=b.getElementsByTagName(e)[0];
      s.parentNode.insertBefore(t,s)
    }(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '1632311901029140');
    fbq('track', 'PageView');
    </script>
    <noscript>
        <img height="1" width="1" style="display:none"
            src="https://www.facebook.com/tr?id=1632311901029140&ev=PageView&noscript=1" />
    </noscript>
    <!-- End Meta Pixel Code -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description"
        content="LevelUp Market menyediakan voucher game, gift card, dan token untuk berbagai game populer dengan pembelian yang mudah dan aman. Dapatkan voucher untuk Google Play, Steam Wallet, Roblox, dan banyak lagi di LevelUp Market.">
    <meta name="keywords"
        content="LevelUp Market, voucher game, gift card, token game, Google Play, Steam Wallet, Roblox, pembelian aman, voucher online">
    <meta name="author" content="LevelUp Market">
    <meta name="title" content="@yield('title') | LevelUp Your Game!" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') | LevelUp Your Game!</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="shortcut icon"
        href="https://res.cloudinary.com/boxityapp/image/upload/v1730810393/levelupgaming/nupfkufgxx6oigtb75ih.png"
        type="image/x-icon">
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
    <link rel="stylesheet" href="backend/css/fontawesome-all.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <!-- Meta Open Graph untuk media sosial -->
    <meta property="og:title" content="LevelUp Market - Voucher Game dan Gift Card Terbaik">
    <meta property="og:description"
        content="Dapatkan voucher game untuk berbagai platform seperti Google Play, Steam, Roblox, dan lainnya. Aman, cepat, dan terpercaya di LevelUp Market!">
    <meta property="og:image"
        content="https://res.cloudinary.com/boxityapp/image/upload/v1730810811/levelupgaming/fiuim0knuvois1udqhnk.png">
    <meta property="og:url" content="https://levelupgamehub.com">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="LevelUp Market">

    <!-- Meta Twitter -->
    <meta name="twitter:card"
        content="https://res.cloudinary.com/boxityapp/image/upload/v1730810811/levelupgaming/fiuim0knuvois1udqhnk.png">
    <meta name="twitter:title" content="LevelUp Market - Voucher Game dan Gift Card Terbaik">
    <meta name="twitter:description"
        content="Dapatkan voucher game untuk berbagai platform seperti Google Play, Steam, Roblox, dan lainnya. Aman, cepat, dan terpercaya di LevelUp Market!">
    <meta name="twitter:image"
        content="https://res.cloudinary.com/boxityapp/image/upload/v1730810811/levelupgaming/fiuim0knuvois1udqhnk.png">
    <meta name="twitter:site" content="@levelupmarket"> <!-- ganti dengan username Twitter jika ada -->

    <!-- Meta tambahan untuk pengindeksan yang lebih baik -->
    <link rel="canonical" href="https://levelupgamehub.com">
    <meta name="theme-color" content="#9CFF1E">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="format-detection" content="telephone=no">
    <meta name="msvalidate.01" content="YOUR_BING_VERIFICATION_CODE">
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-ZMQ2EZKSBP"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-ZMQ2EZKSBP');
    </script>
    <script type="text/javascript">
        (function(c,l,a,r,i,t,y){
            c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
            t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
            y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
        })(window, document, "clarity", "script", "oturnn0i9y");
    </script>
    <!-- Google Tag Manager -->
    <script>
        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-NC65L328');
    </script>
    <!-- End Google Tag Manager -->
    @stack('css')
</head>

<body>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NC65L328" height="0" width="0"
            style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <div class="preloader">
        <div class="loader-inner">
            <div class="loader-circle">
                <img src="https://res.cloudinary.com/boxityapp/image/upload/v1730820799/levelupgaming/donation/ccterurxh9rxeaglqikl.gif"
                    alt="Preloader">
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
                            <a class="site-logo site-title" href="/">
                                <img src="https://res.cloudinary.com/boxityapp/image/upload/v1730810393/levelupgaming/zgsvozukgxyrpnybvg22.png"
                                    alt="site-logo">
                            </a>
                            <button class="logo-btn"><i class="las la-bars"></i></button>
                        </div>
                        <div class="header-search">
                            <div class="header-search-area">
                                <input type="search" class="top-up-search" id="game-search"
                                    placeholder="Cari game kesukaan kamu">
                                <span><i class="las la-search"></i></span>
                            </div>
                            <div class="header-mobile-search-area">
                                <a href="#0" class="header-mobile-search-btn">
                                    <i class="las la-search"></i>
                                </a>
                                <div class="header-mobile-search-form-area">
                                    <input type="search" placeholder="Cari game kesukaan kamu" id="game-search-mobile">
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

    <div class="main-side-menu">
        <div class="main-side-menu-logo-area">
            <div class="thumb-logo">
                <img src="https://res.cloudinary.com/boxityapp/image/upload/v1730810393/levelupgaming/zgsvozukgxyrpnybvg22.png"
                    alt="logo">
            </div>
            <span class="main-side-menu-cross"><i class="las la-times"></i></span>
        </div>
        <ul class="main-side-menu-list">
            <li>
                <a href="/" class="active">
                    <div class="main-side-menu-item">
                        <i class="las la-th-large"></i> Home
                    </div>
                    <span><i class="las la-angle-right"></i></span>
                </a>
            </li>

            <li>
                <a href="/topup" class="">
                    <div class="main-side-menu-item">
                        <i class="las la-coins"></i> Topup
                    </div>
                    <span><i class="las la-angle-right"></i></span>
                </a>
            </li>
            <li>
                <a href="/about" class="">
                    <div class="main-side-menu-item">
                        <i class="las la-info-circle"></i> About
                    </div>
                    <span><i class="las la-angle-right"></i></span>
                </a>
            </li>
            <li>
                <a href="/faq" class="">
                    <div class="main-side-menu-item">
                        <i class="las la-question-circle"></i> FAQ
                    </div>
                    <span><i class="las la-angle-right"></i></span>
                </a>
            </li>
            <li>
                <a href="/contact" class="">
                    <div class="main-side-menu-item">
                        <i class="las la-headset"></i> Contact
                    </div>
                    <span><i class="las la-angle-right"></i></span>
                </a>
            </li>
        </ul>
    </div>

    @yield('content')


    <a href="#" class="scrollToTop">
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
                                <a href="/" class="site-logo site-title">
                                    <img src="https://res.cloudinary.com/boxityapp/image/upload/v1730810393/levelupgaming/zgsvozukgxyrpnybvg22.png"
                                        alt="LevelUp Gaming Market Logo">
                                </a>
                            </div>
                            <div class="footer-content">
                                <p>Masuk ke dunia penuh kemungkinan gaming bersama LevelUp Gaming Market. Bergabunglah
                                    dengan kami dan rasakan pengalaman gaming terbaik, di mana setiap top-up, voucher,
                                    dan token membawa Anda lebih dekat ke petualangan berikutnya!</p>
                            </div>
                            <div class="footer-content-bottom">
                                <ul class="footer-list logo">
                                    <li><a href="javascript:void()"><i class="las la-envelope me-1"></i>
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
                                <li><a href="/about">Tentang Kami</a></li>
                                <li><a href="/privacy-policy">Kebijakan Privasi</a></li>
                                <li><a href="/terms-and-conditions">Syarat dan Ketentuan</a></li>
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
                            <form action="{{ route('review.store') }}" method="post" id="reviewForm">
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
                                    <li
                                        style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px; padding-left: 0;">
                                        {{-- <label for="agreeTerms"
                                            style="line-height: 1.6; display: inline-block;"></label> --}}
                                        <small class="text-muted">Dengan
                                            ini, saya
                                            setuju bahwa review ini dapat digunakan untuk publikasi di Google Review,
                                            media sosial, dan situs web terkait LevelUp Gaming Market.</small>
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
                        <p>&copy; Copyright -<span class="text--base">LevelUp Gaming Market</span> {{ date('Y') }}.</p>
                        <ul class="footer-social-list">
                            <li>
                                <a
                                    href="https://wa.me/6285195922910?text=Halo%20kak%20LevelUp!%20Saya%20mau%20tanya-tanya%20seputar%20voucher%20game%20dan%20cara%20topup%20disini%20dong"><i
                                        class="lab la-whatsapp"></i> Whatsapp</a>
                            </li>
                            <li>
                                <a href="https://www.facebook.com/profile.php?id=61568151335436"><i
                                        class="lab la-facebook-f"></i> Facebook</a>
                            </li>
                            <li>
                                <a href="https://www.instagram.com/levelupmarketgaming"><i class="lab la-instagram"></i>
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
        document.getElementById('agreeTerms').addEventListener('change', function() {
            document.getElementById('submitBtn').disabled = !this.checked;
        });
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    {{-- For Live Search --}}

    <script>
        $(document).ready(function() {
            // Function to handle live search
            function liveSearch(query) {
                if (query.length > 0) {
                    $.ajax({
                        url: "{{ route('games.search') }}",
                        type: 'GET',
                        data: { q: query },
                        success: function(response) {
                            $('#search-results').empty(); // Clear previous results

                            if (response.length > 0) {
                                $.each(response, function(index, game) {
                                    $('#search-results').append(`
                                        <li style="display: flex; align-items: center; gap: 10px;">
                                            <img src="${game.image_url}" alt="${game.name}" style="width: 40px; height: 40px; border-radius: 20%;">
                                            <a href="${game.game_url}" target="_blank">${game.name}</a>
                                        </li>
                                    `);
                                });
                            } else {
                                $('#search-results').append('<li>No games found.</li>');
                            }
                        },
                        error: function() {
                            $('#search-results').empty();
                            $('#search-results').append('<li>Something went wrong. Please try again.</li>');
                        }
                    });
                } else {
                    $('#search-results').empty(); // Clear results if input is empty
                }
            }

            // Event listeners for both desktop and mobile search inputs
            $('#game-search, #game-search-mobile').on('input', function() {
                let query = $(this).val();
                liveSearch(query);
            });
        });
    </script>
</body>

</html>

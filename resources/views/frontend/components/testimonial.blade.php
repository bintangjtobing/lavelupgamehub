<section class="testimonial-section ptb-120 bg_img" data-background="{{ asset('frontend/images/element/bg1.jpg')}}"
    style="background-image: url('{{ asset('frontend/images/element/bg1.jpg')}}')">
    <div class="element-area">
        <img src="{{asset('frontend/images/element/shadow-2.5ab01ec0.svg')}}" alt=""
            title="Ornamen latar ulasan LevelUp Market">
    </div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-6">
                <div class="section-header text-center">
                    <span class="section-sub-titel"><i class="las la-gamepad"></i> Ulasan Pengguna</span>
                    <h2 class="section-title"><span class="text--base">Pengalaman yang Dibagikan Pengunjung</span></h2>
                    <p>Ulasan pengunjung belum diverifikasi terhadap transaksi. Data contoh diberi label demo.</p>
                </div>
            </div>
        </div>
        <div class="testimonial-slider-wrapper">
            <div class="testimonial-slider swiper-container-horizontal">
                <div class="swiper-wrapper">
                    @forelse($reviews as $review)
                    <div class="swiper-slide">
                        <div class="testimonial-item">
                            <div class="testimonial-user-area">
                                <div class="user-area">
                                    @php($reviewName = $review->name)
                                    @php($avatar = Avatar::create($reviewName)->toBase64())
                                    <img alt="Avatar {{ $reviewName }}" title="Ulasan dari {{ $reviewName }}"
                                        src="{{ $avatar }}">

                                </div>
                                <div class="title-area">
                                    <h5>{{ $review->name }}</h5>
                                    <span class="testimonial-date"><i class="las la-history"></i> {{
                                        $review->created_at->format('d-m-Y') }}</span>
                                </div>
                            </div>
                            @if (str_ends_with((string) $review->email, '@example.com'))
                                <span class="lu-review-demo">Contoh ulasan</span>
                            @endif
                            <p>{{ $review->message }}</p>
                            <div class="testimonial-bottom-wrapper">
                                <ul class="testimonial-icon-list">
                                    @for ($i = 0; $i < $review->rating; $i++)
                                        <li><i class="las la-star"></i></li>
                                        @endfor
                                </ul>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="swiper-slide">
                        <div class="testimonial-item">
                            <p>Belum ada ulasan yang dipublikasikan.</p>
                        </div>
                    </div>
                    @endforelse
                </div>
                <div class="slider-nav-area">
                    <div class="slider-prev slider-nav">
                        <i class="las la-arrow-left"></i>
                    </div>
                    <div class="slider-next slider-nav">
                        <i class="las la-arrow-right"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

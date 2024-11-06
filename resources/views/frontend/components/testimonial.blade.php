<section class="testimonial-section ptb-120 bg_img" data-background="{{ asset('frontend/images/element/bg1.jpg')}}"
    style="background-image: url('{{ asset('frontend/images/element/bg1.jpg')}}')">
    <div class="element-area">
        <img src="{{asset('frontend/images/element/shadow-2.5ab01ec0.svg')}}" alt="element - LevelUp Gaming Market">
    </div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-6">
                <div class="section-header text-center">
                    <span class="section-sub-titel"><i class="las la-gamepad"></i> Pendapat Para Gamer</span>
                    <h2 class="section-title"><span class="text--base">Apa Kata Mereka Tentang Kami</span></h2>
                </div>
            </div>
        </div>
        <div class="testimonial-slider-wrapper">
            <div class="testimonial-slider swiper-container-horizontal">
                <div class="swiper-wrapper">
                    @foreach($reviews as $review)
                    <div class="swiper-slide">
                        <div class="testimonial-item">
                            <div class="testimonial-user-area">
                                <div class="user-area">
                                    <img src="{{ asset('frontend/images/default-avatar.png') }}"
                                        alt="{{ $review->name }} - Review LevelUp Gaming Market">
                                </div>
                                <div class="title-area">
                                    <h5>{{ $review->name }}</h5>
                                    <span class="testimonial-date"><i class="las la-history"></i> {{
                                        $review->created_at->format('d-m-Y') }}</span>
                                </div>
                            </div>
                            <h4 class="testimonial-title">User Review</h4>
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
                    @endforeach
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
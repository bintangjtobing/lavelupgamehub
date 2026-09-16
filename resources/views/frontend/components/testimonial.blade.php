@php
    // Ulasan dibagi ke tiga kolom bergantian supaya tinggi tiap kolom seimbang.
    $wallReviews = $reviews->take(36);
    $columns = [[], [], []];
    foreach ($wallReviews as $i => $review) {
        $columns[$i % 3][] = $review;
    }

    $totalReviews = $reviews->count();
    $averageRating = $totalReviews ? round($reviews->avg('rating'), 1) : 0;
    $avatarStack = $reviews->take(4);
@endphp

<section class="lu-section lu-wall-section" aria-labelledby="ulasan-title">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-sub-titel"><i class="las la-gamepad"></i> Ulasan Pengguna</span>
            <h2 class="section-title" id="ulasan-title">
                <span class="text--base">Pengalaman yang Dibagikan Pengunjung</span>
            </h2>
        </div>

        @if ($totalReviews)
            <div class="lu-wall-summary">
                <div class="lu-wall-avatars" aria-hidden="true">
                    @foreach ($avatarStack as $person)
                        @include('frontend.partials.review-avatar', ['name' => $person->name, 'size' => 'sm'])
                    @endforeach
                </div>
                <div class="lu-wall-stars" aria-hidden="true">
                    @for ($i = 0; $i < 5; $i++)
                        <i class="las la-star {{ $i < round($averageRating) ? 'is-on' : '' }}"></i>
                    @endfor
                </div>
                <strong class="lu-wall-score">{{ number_format($averageRating, 1, ',', '.') }}/5</strong>
                <span class="lu-wall-count">dari {{ $totalReviews }} ulasan</span>
            </div>
        @endif

        <p class="lu-wall-note">
            Ulasan pengunjung belum diverifikasi terhadap transaksi. Data contoh diberi label demo.
        </p>

        @if ($totalReviews)
            <div class="lu-wall" id="lu-wall">
                @foreach ($columns as $index => $column)
                    <div class="lu-wall-col lu-wall-col-{{ $index + 1 }}">
                        {{-- Isi kolom digandakan agar perulangan animasinya tidak terlihat patah --}}
                        <div class="lu-wall-track">
                            @for ($pass = 0; $pass < 2; $pass++)
                                @foreach ($column as $review)
                                    <article class="lu-wall-card" @if ($pass === 1) aria-hidden="true" @endif>
                                        <i class="las la-quote-left lu-wall-quote" aria-hidden="true"></i>

                                        <p class="lu-wall-text">{{ $review->message }}</p>

                                        <div class="lu-wall-person">
                                            @include('frontend.partials.review-avatar', ['name' => $review->name])
                                            <div class="lu-wall-meta">
                                                <span class="lu-wall-name">{{ $review->name }}</span>
                                                <span class="lu-wall-sub">
                                                    <span class="lu-wall-mini-stars" aria-hidden="true">
                                                        @for ($i = 0; $i < $review->rating; $i++)★@endfor
                                                    </span>
                                                    <span class="sr-only">{{ $review->rating }} dari 5 bintang</span>
                                                    {{ $review->created_at->translatedFormat('d M Y') }}
                                                </span>
                                            </div>
                                            @if (str_ends_with((string) $review->email, '@example.com'))
                                                <span class="lu-review-demo">Contoh ulasan</span>
                                            @endif
                                        </div>
                                    </article>
                                @endforeach
                            @endfor
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="lu-empty">Belum ada ulasan yang dipublikasikan.</p>
        @endif
    </div>
</section>

@if ($bestSellers->isNotEmpty())
    <section class="lu-section" id="pilihan-populer" aria-labelledby="pilihan-populer-title">
        <div class="container">
            <div class="lu-section-head">
                <h2 class="lu-section-title" id="pilihan-populer-title">
                    <span class="lu-emoji lu-emoji-fire" aria-hidden="true">🎯</span>
                    Pilihan Populer
                </h2>
                <p>Beberapa produk pilihan untuk membantu kamu mulai menjelajahi katalog.</p>
            </div>

            <div class="lu-grid">
                @foreach ($bestSellers as $item)
                    @include('frontend.partials.catalog-card', [
                        'item' => $item,
                        'badge' => 'Pilihan ' . $loop->iteration,
                    ])
                @endforeach
            </div>
        </div>
    </section>
@endif

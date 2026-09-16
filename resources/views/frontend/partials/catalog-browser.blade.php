{{--
    Penjelajah katalog: tab kategori + pencarian + Load More.

    Semua item dicetak ke HTML (bukan diambil lewat AJAX) supaya nama game terbaca
    mesin pencari. Yang belum gilirannya tampil ditandai atribut [hidden], lalu
    JS yang mengatur filter dan penambahan per batch.
--}}
<section class="lu-section" id="katalog" aria-labelledby="katalog-title">
    <div class="container">
        <div class="lu-section-head">
            <h2 class="lu-section-title" id="katalog-title">
                <span class="lu-emoji" aria-hidden="true">🎮</span>
                Jelajahi Katalog Game dan Voucher
            </h2>
        </div>

        <div class="lu-toolbar">
            <div class="lu-tabs" role="tablist" aria-label="Kategori katalog">
                <button type="button" class="lu-tab" role="tab" aria-selected="false" data-filter="new">
                    Baru di Katalog
                </button>
                <button type="button" class="lu-tab" role="tab" aria-selected="true" data-filter="all">
                    Semua <span class="lu-tab-count">{{ $catalog->count() }}</span>
                </button>
                <button type="button" class="lu-tab" role="tab" aria-selected="false" data-filter="game">
                    Game <span class="lu-tab-count">{{ $counts['game'] ?? 0 }}</span>
                </button>
                <button type="button" class="lu-tab" role="tab" aria-selected="false" data-filter="voucher">
                    Voucher <span class="lu-tab-count">{{ $counts['voucher'] ?? 0 }}</span>
                </button>
                <button type="button" class="lu-tab" role="tab" aria-selected="false" data-filter="entertainment">
                    Hiburan <span class="lu-tab-count">{{ $counts['entertainment'] ?? 0 }}</span>
                </button>
            </div>

            <div class="lu-search">
                <i class="las la-search lu-search-icon" aria-hidden="true"></i>
                <label for="catalog-search" class="visually-hidden sr-only">Cari game atau produk</label>
                <input type="search" id="catalog-search" placeholder="Cari game, voucher, atau aplikasi..."
                    autocomplete="off">
                <button type="button" class="lu-search-clear" id="catalog-search-clear" aria-label="Hapus pencarian"
                    hidden>&times;</button>
            </div>
        </div>

        <div class="lu-grid" id="catalog-grid">
            @foreach ($catalog as $item)
                @include('frontend.partials.catalog-card', [
                    'item' => $item,
                    'isNew' => $item->is_new ?? null,
                    'hidden' => true,
                ])
            @endforeach
        </div>

        <p class="lu-empty" id="catalog-empty" hidden>
            Produk tidak ditemukan. Periksa ejaan atau coba kata kunci lain.
        </p>

        <div class="lu-more-wrap">
            <button type="button" class="lu-more" id="catalog-more">Tampilkan Lebih Banyak</button>
        </div>
    </div>
</section>

@push('scripts')
    <script>
        (function () {
            const BATCH = 18;

            const grid = document.getElementById('catalog-grid');
            const search = document.getElementById('catalog-search');
            const clearBtn = document.getElementById('catalog-search-clear');
            const moreBtn = document.getElementById('catalog-more');
            const empty = document.getElementById('catalog-empty');
            const tabs = document.querySelectorAll('.lu-tab');

            if (!grid) return;

            const cards = Array.from(grid.querySelectorAll('.lu-card'));
            let filter = 'all';
            let shown = BATCH;

            function matches(card) {
                if (filter === 'new' && card.dataset.new !== '1') return false;
                if (filter !== 'all' && filter !== 'new' && card.dataset.category !== filter) return false;

                const q = search.value.trim().toLowerCase();
                return q === '' || card.dataset.name.includes(q);
            }

            function render() {
                const matching = cards.filter(matches);

                cards.forEach(card => { card.hidden = true; });
                matching.slice(0, shown).forEach(card => { card.hidden = false; });

                empty.hidden = matching.length > 0;
                moreBtn.parentElement.hidden = matching.length <= shown;
                clearBtn.hidden = search.value === '';
            }

            function reset() {
                shown = BATCH;
                render();
            }

            tabs.forEach(tab => {
                tab.addEventListener('click', function () {
                    tabs.forEach(t => t.setAttribute('aria-selected', 'false'));
                    this.setAttribute('aria-selected', 'true');
                    filter = this.dataset.filter;
                    reset();
                });
            });

            let debounce;
            search.addEventListener('input', function () {
                clearTimeout(debounce);
                debounce = setTimeout(reset, 150);
            });

            clearBtn.addEventListener('click', function () {
                search.value = '';
                search.focus();
                reset();
            });

            moreBtn.addEventListener('click', function () {
                shown += BATCH;
                render();
            });

            render();
        })();
    </script>
@endpush

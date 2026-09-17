{{--
    Section homepage: statistik hero empat game sekaligus, masing-masing
    dengan tombol top up sendiri.

    Game yang sumbernya sedang tidak bisa dibaca tidak muncul, jadi section
    ini menyesuaikan diri tanpa menampilkan tabel kosong.
--}}
@if (!empty($gameStats))
    <section class="lu-section" id="statistik-hero" aria-labelledby="statistik-hero-title">
        <div class="container">
            <div class="lu-section-head">
                <h2 class="lu-section-title" id="statistik-hero-title">
                    <span class="lu-emoji" aria-hidden="true">📊</span>
                    Hero Meta Terkini
                </h2>
                <p>
                    Hero yang sedang kuat di {{ count($gameStats) }} game populer, langsung dari sumber
                    masing-masing. Pilih gamenya, lalu top up di halaman produknya.
                </p>
            </div>

            <div class="lu-gs-grid">
                @foreach ($gameStats as $game)
                    @include('frontend.partials.game-stats-table', ['game' => $game])
                @endforeach
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            (function () {
                /*
                 * Satu penangan untuk semua tabel statistik. Tiap tabel
                 * menyimpan halamannya sendiri, jadi navigasinya tidak saling
                 * memengaruhi.
                 */
                document.querySelectorAll('[data-game-stats]').forEach(function (box) {
                    const nav = box.querySelector('.lu-gs-nav');
                    if (!nav) return;

                    const rows = Array.from(box.querySelectorAll('tbody tr'));
                    const pages = Math.max(...rows.map(r => Number(r.dataset.page) || 1));
                    const status = box.querySelector('[data-gs-status]');
                    const prev = box.querySelector('[data-gs-prev]');
                    const next = box.querySelector('[data-gs-next]');
                    let page = 1;

                    // Navigasi baru tampil setelah JavaScript siap; tanpanya
                    // seluruh baris tetap terbaca apa adanya.
                    nav.hidden = false;

                    function render() {
                        rows.forEach(r => { r.hidden = Number(r.dataset.page) !== page; });
                        status.textContent = page + ' / ' + pages;
                        prev.disabled = page === 1;
                        next.disabled = page === pages;
                    }

                    prev.addEventListener('click', () => { if (page > 1) { page--; render(); } });
                    next.addEventListener('click', () => { if (page < pages) { page++; render(); } });

                    render();
                });
            })();
        </script>
    @endpush
@endif

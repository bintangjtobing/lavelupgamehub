{{--
    Tabel peringkat hero di kolom samping, sepuluh baris per halaman.

    Semua hero dicetak sekaligus lalu disembunyikan per halaman oleh JavaScript.
    Jumlahnya kecil, jadi memuat semuanya di muka lebih ringan daripada bolak-balik
    ke server, dan daftarnya tetap terbaca bila JavaScript dimatikan.
--}}
@if (!empty($heroStats['heroes']))
    @php
        $perPage = 10;
        $heroes = $heroStats['heroes'];
        $pages = (int) ceil(count($heroes) / $perPage);
    @endphp

    <section class="lu-rank" aria-labelledby="rank-title" data-per-page="{{ $perPage }}">
        <div class="lu-rank-head">
            <h2 id="rank-title">Peringkat Hero</h2>
            <span>Win rate tertinggi &middot; {{ config("mlbb.ranges.{$heroDays}.label") }} terakhir</span>
        </div>

        <table class="lu-rank-table">
            <thead>
                <tr>
                    <th scope="col" class="c-no">#</th>
                    <th scope="col">Hero</th>
                    <th scope="col" class="c-num">Win</th>
                    <th scope="col" class="c-num">Pick</th>
                    <th scope="col" class="c-num">Ban</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($heroes as $i => $hero)
                    <tr data-page="{{ (int) floor($i / $perPage) + 1 }}"
                        @if ($i >= $perPage) hidden @endif>
                        <td class="c-no">{{ $i + 1 }}</td>
                        <td class="c-hero">
                            @if ($hero['image'])
                                <img src="{{ $hero['image'] }}" alt="" loading="lazy" decoding="async"
                                    width="24" height="24">
                            @endif
                            <span>{{ $hero['name'] }}</span>
                        </td>
                        <td class="c-num win">{{ number_format($hero['win_rate'], 1, ',', '.') }}%</td>
                        <td class="c-num pick">{{ number_format($hero['pick_rate'], 1, ',', '.') }}%</td>
                        <td class="c-num ban">{{ number_format($hero['ban_rate'], 1, ',', '.') }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if ($pages > 1)
            <div class="lu-rank-nav" hidden>
                <button type="button" data-rank-prev aria-label="Halaman sebelumnya">&larr;</button>
                <span data-rank-status>1 / {{ $pages }}</span>
                <button type="button" data-rank-next aria-label="Halaman berikutnya">&rarr;</button>
            </div>
        @endif
    </section>

    @push('scripts')
        <script>
            (function () {
                const box = document.querySelector('.lu-rank');
                if (!box) return;

                const nav = box.querySelector('.lu-rank-nav');
                if (!nav) return;

                const rows = Array.from(box.querySelectorAll('tbody tr'));
                const pages = Math.max(...rows.map(r => Number(r.dataset.page) || 1));
                const status = box.querySelector('[data-rank-status]');
                const prev = box.querySelector('[data-rank-prev]');
                const next = box.querySelector('[data-rank-next]');
                let page = 1;

                // Navigasi baru ditampilkan setelah JavaScript siap; tanpanya
                // seluruh daftar tetap terbaca apa adanya.
                nav.hidden = false;

                function render() {
                    rows.forEach(row => { row.hidden = Number(row.dataset.page) !== page; });
                    status.textContent = page + ' / ' + pages;
                    prev.disabled = page === 1;
                    next.disabled = page === pages;
                }

                prev.addEventListener('click', () => { if (page > 1) { page--; render(); } });
                next.addEventListener('click', () => { if (page < pages) { page++; render(); } });

                render();
            })();
        </script>
    @endpush
@endif

{{--
    Peringkat hero Mobile Legends.

    Sumbernya API resmi Moonton yang juga dipakai mobilelegends.com. Kalau
    permintaannya gagal, $heroStats bernilai null dan bagian ini tidak dirender
    sama sekali -- halaman produk tidak boleh ikut jatuh karena statistik.
--}}
@if (!empty($heroStats['heroes']))
    @php
        $heroes = $heroStats['heroes'];
        $metric = $heroMetric ?? 'win_rate';

        $metrics = [
            'win_rate' => ['label' => 'Win Rate', 'short' => 'Win'],
            'pick_rate' => ['label' => 'Pick Rate', 'short' => 'Pick'],
            'ban_rate' => ['label' => 'Ban Rate', 'short' => 'Ban'],
        ];

        // Batang dinormalkan terhadap nilai tertinggi pada metrik yang dipilih.
        // Tanpa itu, pick rate yang jarang menembus 3% akan tampak rata semua.
        $peak = max(1, collect($heroes)->max($metric));
    @endphp

    <section class="lu-hero-stats" id="peringkat-hero" aria-labelledby="peringkat-hero-title">
        <div class="lu-hero-head">
            <div>
                <h2 id="peringkat-hero-title">Peringkat Hero</h2>
                <p>
                    Data resmi Moonton, {{ config("mlbb.ranges.{$heroDays}.label") }} terakhir
                    &middot; rank {{ config("mlbb.ranks.{$heroRank}") }}
                    &middot; {{ $heroStats['total'] }} hero
                </p>
            </div>
        </div>

        {{-- Penyaring bekerja lewat alamat halaman, jadi tetap berfungsi
             walaupun JavaScript dimatikan dan bisa ditautkan langsung. --}}
        <div class="lu-hero-filters">
            <div class="lu-hero-group" role="group" aria-label="Urutkan">
                @foreach ($metrics as $key => $m)
                    <a href="{{ request()->fullUrlWithQuery(['urut' => $key]) }}#peringkat-hero"
                        class="{{ $metric === $key ? 'is-on' : '' }}">{{ $m['label'] }}</a>
                @endforeach
            </div>

            <div class="lu-hero-group" role="group" aria-label="Rentang waktu">
                @foreach (config('mlbb.ranges') as $days => $range)
                    <a href="{{ request()->fullUrlWithQuery(['hari' => $days]) }}#peringkat-hero"
                        class="{{ (int) $heroDays === (int) $days ? 'is-on' : '' }}">{{ $range['label'] }}</a>
                @endforeach
            </div>

            <div class="lu-hero-group" role="group" aria-label="Tingkatan rank">
                @foreach (config('mlbb.ranks') as $value => $label)
                    <a href="{{ request()->fullUrlWithQuery(['rank' => $value]) }}#peringkat-hero"
                        class="{{ (int) $heroRank === (int) $value ? 'is-on' : '' }}">{{ $label }}</a>
                @endforeach
            </div>
        </div>

        <ol class="lu-hero-list">
            @foreach ($heroes as $i => $hero)
                <li class="lu-hero-row">
                    <span class="lu-hero-rank">{{ $i + 1 }}</span>

                    <span class="lu-hero-face">
                        @if ($hero['image'])
                            <img src="{{ $hero['image'] }}" alt="{{ $hero['name'] }}" loading="lazy"
                                decoding="async" width="48" height="48">
                        @else
                            <span class="lu-hero-initial">{{ mb_substr($hero['name'], 0, 1) }}</span>
                        @endif
                    </span>

                    <span class="lu-hero-name">{{ $hero['name'] }}</span>

                    <span class="lu-hero-bar" aria-hidden="true">
                        <span class="fill lu-metric-{{ str_replace('_rate', '', $metric) }}"
                            style="width: {{ max(2, round($hero[$metric] / $peak * 100)) }}%"></span>
                    </span>

                    <span class="lu-hero-figures">
                        @foreach ($metrics as $key => $m)
                            <span class="{{ $metric === $key ? 'is-active' : '' }}">
                                <b>{{ number_format($hero[$key], 2, ',', '.') }}%</b>
                                <small>{{ $m['short'] }}</small>
                            </span>
                        @endforeach
                    </span>
                </li>
            @endforeach
        </ol>

        <p class="lu-hero-note">
            Statistik berasal langsung dari Moonton dan diperbarui berkala.
            Angka ini menggambarkan performa hero pada pertandingan rank, bukan jaminan hasil.
        </p>
    </section>
@endif

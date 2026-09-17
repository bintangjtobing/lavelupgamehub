{{--
    Ticker peringkat hero Mobile Legends.

    Sumbernya API resmi Moonton. Kalau permintaannya gagal, $heroStats bernilai
    null dan bagian ini tidak dirender sama sekali -- panel pembayaran di
    sebelahnya tidak boleh ikut terganggu oleh statistik.
--}}
@if (!empty($heroStats['heroes']))
    <section class="lu-ticker" aria-labelledby="ticker-title">
        <div class="lu-ticker-head">
            <h2 id="ticker-title">Peringkat Hero</h2>
            <span>Win rate tertinggi &middot; {{ config("mlbb.ranges.{$heroDays}.label") }} terakhir</span>
        </div>

        <div class="lu-ticker-window">
            {{-- Isi digandakan agar perulangan gerakannya tidak terlihat patah --}}
            <div class="lu-ticker-track">
                @for ($pass = 0; $pass < 2; $pass++)
                    @foreach ($heroStats['heroes'] as $hero)
                        <span class="lu-ticker-item" @if ($pass === 1) aria-hidden="true" @endif>
                            @if ($hero['image'])
                                <img src="{{ $hero['image'] }}" alt="" loading="lazy" decoding="async"
                                    width="26" height="26">
                            @endif
                            <b>{{ $hero['name'] }}</b>
                            <i class="win">{{ number_format($hero['win_rate'], 1, ',', '.') }}%</i>
                            <i class="pick">{{ number_format($hero['pick_rate'], 1, ',', '.') }}%</i>
                            <i class="ban">{{ number_format($hero['ban_rate'], 1, ',', '.') }}%</i>
                        </span>
                    @endforeach
                @endfor
            </div>
        </div>

        <div class="lu-ticker-legend" aria-hidden="true">
            <span><i class="win"></i> Win</span>
            <span><i class="pick"></i> Pick</span>
            <span><i class="ban"></i> Ban</span>
        </div>
    </section>
@endif

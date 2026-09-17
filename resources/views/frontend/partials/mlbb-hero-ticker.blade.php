{{--
    Baris berjalan peringkat hero, selebar layar, tepat di bawah menu navigasi.
    Dititipkan lewat @push('subheader') dari halaman produk Mobile Legends.
--}}
@if (!empty($heroStats['heroes']))
    <div class="lu-strip" role="complementary" aria-label="Peringkat hero Mobile Legends">
        <span class="lu-strip-tag">Peringkat Hero</span>

        <div class="lu-strip-window">
            {{-- Isi digandakan agar perulangan gerakannya tidak terlihat patah --}}
            <div class="lu-strip-track">
                @for ($pass = 0; $pass < 2; $pass++)
                    @foreach ($heroStats['heroes'] as $hero)
                        <span class="lu-strip-item" @if ($pass === 1) aria-hidden="true" @endif>
                            @if ($hero['image'])
                                <img src="{{ $hero['image'] }}" alt="" loading="lazy" decoding="async"
                                    width="22" height="22">
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
    </div>
@endif

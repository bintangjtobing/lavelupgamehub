{{--
    Baris berjalan statistik hero, selebar layar, tepat di bawah menu navigasi.
    Dititipkan lewat @push('subheader') dari halaman produk.

    Game yang sumbernya tidak menyediakan angka (LoL) menampilkan peran
    champion sebagai gantinya, bukan angka nol.
--}}
@if (!empty($game['rows']))
    <div class="lu-strip" role="complementary" aria-label="Statistik hero {{ $game['label'] }}">
        <span class="lu-strip-tag">{{ $game['label'] }}</span>

        <div class="lu-strip-window">
            {{-- Isi digandakan agar perulangan gerakannya tidak terlihat patah --}}
            <div class="lu-strip-track">
                @for ($pass = 0; $pass < 2; $pass++)
                    @foreach ($game['rows'] as $row)
                        <span class="lu-strip-item" @if ($pass === 1) aria-hidden="true" @endif>
                            @if ($row['image'])
                                <img src="{{ $row['image'] }}" alt="" loading="lazy" decoding="async"
                                    width="22" height="22">
                            @endif
                            <b>{{ $row['name'] }}</b>
                            @foreach ($game['columns'] as $col)
                                @if ($col === 'role')
                                    <i class="role">{{ $row['role'] ?: '—' }}</i>
                                @elseif ($row[$col] !== null)
                                    <i class="{{ $col }}">{{ number_format($row[$col], 1, ',', '.') }}%</i>
                                @endif
                            @endforeach
                        </span>
                    @endforeach
                @endfor
            </div>
        </div>
    </div>
@endif

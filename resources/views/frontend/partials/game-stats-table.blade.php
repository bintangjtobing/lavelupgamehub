{{--
    Tabel statistik satu game, sepuluh baris per halaman.

    Variabel: $game (dari GameStatsManager), $compact (opsional)

    Kolom mengikuti $game['columns'] karena tiap sumber menyediakan hal yang
    berbeda. Kolom yang tidak tersedia tidak ditampilkan sama sekali -- bukan
    diisi nol, supaya tidak ada angka yang terbaca sebagai fakta padahal
    sumbernya memang tidak punya.
--}}
@php
    $perPage = 10;
    $rows = $game['rows'];
    $pages = (int) ceil(count($rows) / $perPage);
    $id = 'gs-' . $game['key'];

    $heads = ['win' => 'Win', 'pick' => 'Pick', 'ban' => 'Ban', 'role' => 'Role'];
@endphp

<div class="lu-gs" id="{{ $id }}" data-game-stats>
    <div class="lu-gs-head">
        <h3>{{ $game['label'] }}</h3>
        @if ($game['note'])
            <p>{{ $game['note'] }}</p>
        @endif
    </div>

    <table class="lu-gs-table">
        <thead>
            <tr>
                <th scope="col" class="c-no">#</th>
                <th scope="col">Hero</th>
                @foreach ($game['columns'] as $col)
                    <th scope="col" class="{{ $col === 'role' ? 'c-role' : 'c-num' }}">{{ $heads[$col] ?? $col }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $i => $row)
                <tr data-page="{{ (int) floor($i / $perPage) + 1 }}" @if ($i >= $perPage) hidden @endif>
                    <td class="c-no">{{ $i + 1 }}</td>
                    <td class="c-hero">
                        @if ($row['image'])
                            <img src="{{ $row['image'] }}" alt="" loading="lazy" decoding="async"
                                width="24" height="24">
                        @endif
                        <span>{{ $row['name'] }}</span>
                    </td>
                    @foreach ($game['columns'] as $col)
                        @if ($col === 'role')
                            <td class="c-role">{{ $row['role'] ?: '—' }}</td>
                        @else
                            <td class="c-num {{ $col }}">
                                {{ $row[$col] === null ? '—' : number_format($row[$col], 1, ',', '.') . '%' }}
                            </td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="lu-gs-foot">
        @if ($pages > 1)
            <div class="lu-gs-nav" hidden>
                <button type="button" data-gs-prev aria-label="Halaman sebelumnya">&larr;</button>
                <span data-gs-status>1 / {{ $pages }}</span>
                <button type="button" data-gs-next aria-label="Halaman berikutnya">&rarr;</button>
            </div>
        @endif

        @if ($game['item'])
            <a href="{{ url('/topup/' . $game['item']->slug) }}" class="lu-gs-cta"
                title="Top up {{ $game['label'] }} di LevelUp Market">
                {{ $game['cta'] }} <span aria-hidden="true">&rarr;</span>
            </a>
        @endif
    </div>

    @if ($game['source'])
        <p class="lu-gs-source">Sumber: {{ $game['source'] }}</p>
    @endif
</div>

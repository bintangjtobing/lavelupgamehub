@extends('admin.layout')
@section('title', 'Google Insight')

@php
    $n = fn ($v, $d = 0) => number_format((float) $v, $d, ',', '.');
    $ga4 = $data['ga4'];
    $gsc = $data['gsc'];
    $combined = $data['combined'];
@endphp

@section('content')
    <div class="lup-head">
        <div>
            <h1>Google Insight</h1>
            <p>Search Console dan GA4 disandingkan dengan data pesanan kita sendiri</p>
        </div>
        <div class="lup-range">
            @foreach ([7 => '7 hari', 28 => '28 hari', 90 => '90 hari'] as $d => $label)
                <a href="{{ route('admin.insights', ['hari' => $d]) }}"
                    class="{{ $days === $d ? 'is-on' : '' }}">{{ $label }}</a>
            @endforeach
        </div>
    </div>

    {{-- Panduan muncul selama salah satu sumber belum tersambung --}}
    @if (! $gsc['connected'] || ! $ga4['connected'])
        <div class="lup-panel" style="margin-bottom:18px">
            <h2>Menyambungkan sumber data</h2>
            <p class="hint">Bagian di bawah akan terisi sendiri begitu aksesnya diberikan.</p>

            <dl class="lup-kv">
                <dt>Search Console</dt>
                <dd>
                    @if ($gsc['connected'])
                        <span class="lup-badge completed">tersambung</span>
                    @else
                        <span class="lup-badge failed">belum</span>
                        @if ($gsc['error'])
                            <div class="lup-muted" style="margin-top:6px">{{ $gsc['error'] }}</div>
                        @endif
                    @endif
                </dd>

                <dt>Google Analytics 4</dt>
                <dd>
                    @if ($ga4['connected'])
                        <span class="lup-badge completed">tersambung</span>
                    @else
                        <span class="lup-badge failed">belum</span>
                        @if ($ga4['error'])
                            <div class="lup-muted" style="margin-top:6px">{{ $ga4['error'] }}</div>
                        @endif
                    @endif
                </dd>

                @if ($data['service_account'])
                    <dt>Beri akses ke</dt>
                    <dd class="lup-mono">{{ $data['service_account'] }}</dd>
                @endif
            </dl>
        </div>
    @endif

    {{-- Rantai lengkap: pencarian -> situs -> pesanan --}}
    <div class="lup-panel" style="margin-bottom:18px">
        <h2>Dari pencarian sampai pesanan</h2>
        <p class="hint">
            Tiap langkah diberi tanda sumbernya. Angka antar sumber tidak akan pernah sama persis:
            GA4 kehilangan pengunjung yang memblokir skrip, dan Search Console tertinggal beberapa hari.
        </p>

        <div class="lup-funnel">
            @foreach ($combined['steps'] as $step)
                <div class="lup-step">
                    <div class="bar" style="width: {{ max(2, $step['of_first']) }}%"></div>
                    <div class="row">
                        <span class="name">{{ $step['label'] }}
                            <span class="lup-badge" style="margin-left:6px">{{ $step['source'] }}</span>
                        </span>
                        <span class="num">{{ $n($step['value']) }}</span>
                    </div>
                    @if ($step['of_previous'] !== null)
                        <div class="meta">{{ $n($step['of_previous'], 1) }}% lanjut dari langkah sebelumnya</div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="lup-stats" style="margin-top:18px; margin-bottom:0">
            @if ($combined['search_to_order'] !== null)
                <div class="lup-stat">
                    <div class="k">Klik pencarian &rarr; pesanan</div>
                    <div class="v">{{ $n($combined['search_to_order'], 2) }}%</div>
                    <div class="s">konversi dari trafik organik</div>
                </div>
            @endif
            <div class="lup-stat">
                <div class="k">Kunjungan &rarr; pesanan</div>
                <div class="v">{{ $n($combined['visit_to_order'], 2) }}%</div>
                <div class="s">konversi keseluruhan</div>
            </div>
            @if ($combined['checkout_to_order'] !== null)
                <div class="lup-stat">
                    <div class="k">Checkout &rarr; pesanan</div>
                    <div class="v">{{ $n($combined['checkout_to_order'], 1) }}%</div>
                    <div class="s">yang benar-benar menyelesaikan bayar</div>
                </div>
            @endif
        </div>
    </div>

    {{-- Search Console --}}
    @if ($gsc['connected'])
        <div class="lup-stats">
            <div class="lup-stat">
                <div class="k">Impresi</div>
                <div class="v">{{ $n($gsc['summary']['impressions']) }}</div>
                <div class="s">muncul di hasil pencarian</div>
            </div>
            <div class="lup-stat">
                <div class="k">Klik</div>
                <div class="v">{{ $n($gsc['summary']['clicks']) }}</div>
                <div class="s">sampai {{ $gsc['summary']['until'] }}</div>
            </div>
            <div class="lup-stat">
                <div class="k">CTR</div>
                <div class="v">{{ $n($gsc['summary']['ctr'], 2) }}%</div>
                <div class="s">klik per impresi</div>
            </div>
            <div class="lup-stat">
                <div class="k">Posisi rata-rata</div>
                <div class="v">{{ $n($gsc['summary']['position'], 1) }}</div>
                <div class="s">makin kecil makin baik</div>
            </div>
        </div>

        <div class="lup-grid">
            <div class="lup-panel">
                <h2>Kata kunci</h2>
                <p class="hint">Kata yang diketik orang sebelum menemukan situsmu.</p>
                <div class="lup-scroll">
                    <table class="lup-table">
                        <thead>
                            <tr>
                                <th>Kata kunci</th>
                                <th class="num">Klik</th>
                                <th class="num">Impresi</th>
                                <th class="num">CTR</th>
                                <th class="num">Posisi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($gsc['queries'] as $row)
                                <tr>
                                    <td>{{ $row['query'] }}</td>
                                    <td class="num">{{ $n($row['clicks']) }}</td>
                                    <td class="num">{{ $n($row['impressions']) }}</td>
                                    <td class="num">{{ $n($row['ctr'], 1) }}%</td>
                                    <td class="num">{{ $n($row['position'], 1) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="lup-muted">Belum ada kata kunci tercatat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="lup-panel">
                <h2>Halaman di pencarian</h2>
                <p class="hint">Halaman mana yang dijangkau lewat Google.</p>
                <div class="lup-scroll">
                    <table class="lup-table">
                        <thead>
                            <tr>
                                <th>Halaman</th>
                                <th class="num">Klik</th>
                                <th class="num">Impresi</th>
                                <th class="num">Posisi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($gsc['pages'] as $row)
                                <tr>
                                    <td class="lup-mono">{{ $row['path'] }}</td>
                                    <td class="num">{{ $n($row['clicks']) }}</td>
                                    <td class="num">{{ $n($row['impressions']) }}</td>
                                    <td class="num">{{ $n($row['position'], 1) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="lup-muted">Belum ada halaman tercatat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    {{-- GA4 --}}
    @if ($ga4['connected'])
        <div class="lup-stats">
            <div class="lup-stat">
                <div class="k">Pengguna (GA4)</div>
                <div class="v">{{ $n($ga4['summary']['users']) }}</div>
                <div class="s">{{ $n($ga4['summary']['new_users']) }} pengguna baru</div>
            </div>
            <div class="lup-stat">
                <div class="k">Sesi (GA4)</div>
                <div class="v">{{ $n($ga4['summary']['sessions']) }}</div>
                <div class="s">{{ $n($ga4['summary']['page_views']) }} tayangan halaman</div>
            </div>
            <div class="lup-stat">
                <div class="k">Keterlibatan</div>
                <div class="v">{{ $n($ga4['summary']['engagement_rate'], 1) }}%</div>
                <div class="s">pentalan {{ $n($ga4['summary']['bounce_rate'], 1) }}%</div>
            </div>
            <div class="lup-stat">
                <div class="k">Rata-rata durasi</div>
                <div class="v">{{ gmdate('i:s', (int) $ga4['summary']['avg_duration']) }}</div>
                <div class="s">menit per sesi</div>
            </div>
        </div>

        <div class="lup-grid">
            <div class="lup-panel">
                <h2>Saluran trafik</h2>
                <p class="hint">Pengelompokan otomatis dari GA4.</p>
                <div class="lup-scroll">
                    <table class="lup-table">
                        <thead>
                            <tr>
                                <th>Saluran</th>
                                <th class="num">Sesi</th>
                                <th class="num">Pengguna</th>
                                <th class="num">Keterlibatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($ga4['channels'] as $row)
                                <tr>
                                    <td>{{ $row['label'] }}</td>
                                    <td class="num">{{ $n($row['sessions']) }}</td>
                                    <td class="num">{{ $n($row['users']) }}</td>
                                    <td class="num">{{ $n($row['engagement_rate'], 1) }}%</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="lup-muted">Belum ada data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="lup-panel">
                <h2>Event</h2>
                <p class="hint">
                    <strong>purchase</strong> dikirim dari server saat webhook Saweria masuk,
                    karena pembayaran selesai di luar situs kita.
                </p>
                <div class="lup-scroll">
                    <table class="lup-table">
                        <thead>
                            <tr>
                                <th>Nama event</th>
                                <th class="num">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($ga4['events'] as $row)
                                <tr>
                                    <td class="lup-mono">{{ $row['name'] }}</td>
                                    <td class="num">{{ $n($row['count']) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="lup-muted">Belum ada event.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
@endsection

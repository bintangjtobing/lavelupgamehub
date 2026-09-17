@extends('admin.layout')
@section('title', 'Analitik')

@php
    $rp = fn ($n) => 'Rp' . number_format((int) $n, 0, ',', '.');
    $maxDaily = max(1, collect($daily)->max('sessions'));
@endphp

@section('content')
    <div class="ad-head">
        <div>
            <h1>Analitik</h1>
            <p>{{ $days }} hari terakhir &middot; diperbarui {{ now()->translatedFormat('d M Y, H:i') }}</p>
        </div>
        <div class="ad-range">
            @foreach ([7 => '7 hari', 30 => '30 hari', 90 => '90 hari'] as $d => $label)
                <a href="{{ route('admin.dashboard', ['hari' => $d]) }}"
                    class="{{ $days === $d ? 'is-on' : '' }}">{{ $label }}</a>
            @endforeach
        </div>
    </div>

    <div class="ad-stats">
        <div class="ad-stat">
            <div class="k">Kunjungan</div>
            <div class="v">{{ number_format($summary['sessions'], 0, ',', '.') }}</div>
            <div class="s">{{ number_format($summary['page_views'], 0, ',', '.') }} halaman dibuka</div>
        </div>
        <div class="ad-stat">
            <div class="k">Menuju checkout</div>
            <div class="v">{{ number_format($summary['checkout_clicks'], 0, ',', '.') }}</div>
            <div class="s">dari {{ number_format($summary['product_views'], 0, ',', '.') }} kali lihat produk</div>
        </div>
        <div class="ad-stat">
            <div class="k">Pesanan</div>
            <div class="v">{{ number_format($summary['orders'], 0, ',', '.') }}</div>
            <div class="s">{{ number_format($summary['orders_paid'], 0, ',', '.') }} sudah dibayar</div>
        </div>
        <div class="ad-stat">
            <div class="k">Nilai transaksi</div>
            <div class="v">{{ $rp($summary['revenue']) }}</div>
            <div class="s">komisi {{ $rp($summary['commission']) }}</div>
        </div>
        <div class="ad-stat">
            <div class="k">Konversi</div>
            <div class="v">{{ number_format($summary['conversion'], 2, ',', '.') }}%</div>
            <div class="s">kunjungan yang berakhir dibayar</div>
        </div>
    </div>

    <div class="ad-grid">
        <div class="ad-panel">
            <h2>Funnel</h2>
            <p class="hint">
                Dihitung per kunjungan, bukan per klik. Satu orang yang membuka lima produk
                tetap dihitung satu.
            </p>

            <div class="ad-funnel">
                @foreach ($funnel as $step)
                    <div class="ad-step">
                        <div class="bar" style="width: {{ max(2, $step['of_total']) }}%"></div>
                        <div class="row">
                            <span class="name">{{ $step['label'] }}</span>
                            <span class="num">{{ number_format($step['count'], 0, ',', '.') }}</span>
                        </div>
                        <div class="meta">
                            {{ number_format($step['of_total'], 1, ',', '.') }}% dari kunjungan
                            @if ($step['of_previous'] !== null)
                                &middot; {{ number_format($step['of_previous'], 1, ',', '.') }}% lanjut dari langkah
                                sebelumnya
                                @if ($step['drop'] > 0)
                                    &middot; <span class="drop">{{ number_format($step['drop'], 0, ',', '.') }}
                                        berhenti</span>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="ad-panel">
            <h2>Sumber trafik</h2>
            <p class="hint">
                Diambil dari UTM saat kunjungan pertama. Tanpa UTM, dipakai domain perujuk.
            </p>

            <div class="ad-scroll">
                <table class="ad-table">
                    <thead>
                        <tr>
                            <th>Sumber</th>
                            <th class="num">Kunjungan</th>
                            <th class="num">Pesanan</th>
                            <th class="num">Konversi</th>
                            <th class="num">Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sources as $row)
                            <tr>
                                <td>{{ $row['label'] }}</td>
                                <td class="num">{{ number_format($row['sessions'], 0, ',', '.') }}</td>
                                <td class="num">{{ number_format($row['orders'], 0, ',', '.') }}</td>
                                <td class="num">{{ number_format($row['conversion'], 2, ',', '.') }}%</td>
                                <td class="num">{{ $rp($row['revenue']) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="ad-muted">Belum ada kunjungan tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="ad-grid">
        <div class="ad-panel">
            <h2>Produk paling diminati</h2>
            <p class="hint">Rasio = berapa persen yang melihat lalu menuju checkout.</p>

            <div class="ad-scroll">
                <table class="ad-table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th class="num">Dilihat</th>
                            <th class="num">Ke checkout</th>
                            <th class="num">Rasio</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $row)
                            <tr>
                                <td><a href="{{ url('/topup/' . $row['slug']) }}" target="_blank"
                                        rel="noopener">{{ $row['slug'] }}</a></td>
                                <td class="num">{{ number_format($row['views'], 0, ',', '.') }}</td>
                                <td class="num">{{ number_format($row['clicks'], 0, ',', '.') }}</td>
                                <td class="num">{{ number_format($row['rate'], 1, ',', '.') }}%</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="ad-muted">Belum ada produk yang dibuka.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="ad-panel">
            <h2>Harian</h2>
            <p class="hint">Tinggi batang = kunjungan. Batang terang = ada pesanan hari itu.</p>

            <div class="ad-spark">
                @foreach ($daily as $day)
                    <div class="{{ $day['orders'] > 0 ? 'has-order' : '' }}"
                        style="height: {{ max(2, round($day['sessions'] / $maxDaily * 100)) }}%"
                        title="{{ $day['date']->translatedFormat('d M') }}: {{ $day['sessions'] }} kunjungan, {{ $day['orders'] }} pesanan">
                    </div>
                @endforeach
            </div>
            <p class="hint" style="margin-top:12px">
                {{ $daily[0]['date']->translatedFormat('d M') }} &ndash;
                {{ end($daily)['date']->translatedFormat('d M Y') }}
            </p>
        </div>
    </div>

    <div class="ad-panel">
        <h2>Pesanan terbaru</h2>
        <p class="hint">
            @if ($summary['orders'] > 0)
                {{ $summary['attributed'] }} dari {{ $summary['orders'] }} pesanan berhasil dihubungkan ke kunjungan.
            @else
                Belum ada pesanan pada rentang ini.
            @endif
        </p>

        <div class="ad-scroll">
            <table class="ad-table">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Produk</th>
                        <th>Sumber</th>
                        <th>Status</th>
                        <th class="num">Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentOrders as $order)
                        <tr>
                            <td class="ad-muted">{{ ($order->ordered_at ?: $order->created_at)->translatedFormat('d M, H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.orders.show', $order->saweria_id) }}">
                                    {{ $order->product_name ?: 'Menunggu detail' }}
                                </a>
                                @if ($order->game_name)
                                    <div class="ad-muted">{{ $order->game_name }}</div>
                                @endif
                            </td>
                            <td class="ad-muted">{{ $order->source_label }}</td>
                            <td><span class="ad-badge {{ $order->state }}">{{ $order->state_label }}</span></td>
                            <td class="num">{{ $rp($order->amount_raw) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="ad-muted">Belum ada pesanan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

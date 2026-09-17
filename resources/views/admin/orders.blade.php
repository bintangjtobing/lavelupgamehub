@extends('admin.layout')
@section('title', 'Pesanan')

@php
    $rp = fn ($n) => 'Rp' . number_format((int) $n, 0, ',', '.');
    $states = [
        'semua' => 'Semua',
        'completed' => 'Selesai',
        'processing' => 'Diproses',
        'pending' => 'Menunggu bayar',
        'failed' => 'Gagal',
        'expired' => 'Kedaluwarsa',
    ];
@endphp

@section('content')
    <div class="ad-head">
        <div>
            <h1>Pesanan</h1>
            <p>{{ number_format($total, 0, ',', '.') }} pesanan tercatat lewat webhook Saweria</p>
        </div>
    </div>

    <div class="ad-filters">
        <div class="ad-chips">
            @foreach ($states as $key => $label)
                <a href="{{ route('admin.orders', array_filter(['status' => $key === 'semua' ? null : $key, 'cari' => $search ?: null])) }}"
                    class="{{ $state === $key ? 'is-on' : '' }}">
                    {{ $label }}
                    @if ($key !== 'semua')
                        <span class="ad-muted">{{ $counts[$key] ?? 0 }}</span>
                    @endif
                </a>
            @endforeach
        </div>

        <form method="get" action="{{ route('admin.orders') }}" class="ad-search">
            @if ($state !== 'semua')
                <input type="hidden" name="status" value="{{ $state }}">
            @endif
            <input type="search" name="cari" value="{{ $search }}"
                placeholder="Cari Track ID, nama, email, game...">
            <button type="submit">Cari</button>
        </form>
    </div>

    @if ($orders->isEmpty())
        <div class="ad-empty">
            @if ($search !== '' || $state !== 'semua')
                Tidak ada pesanan yang cocok dengan penyaring ini.
            @else
                Belum ada pesanan masuk. Begitu ada pembelian lewat toko Saweria,
                pesanannya akan muncul di sini secara otomatis.
            @endif
        </div>
    @else
        <div class="ad-panel">
            <div class="ad-scroll">
                <table class="ad-table">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Produk</th>
                            <th>Pembeli</th>
                            <th>Sumber</th>
                            <th>Status</th>
                            <th class="num">Nilai</th>
                            <th class="num">Komisi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td class="ad-muted">
                                    {{ ($order->ordered_at ?: $order->created_at)->translatedFormat('d M Y') }}
                                    <div>{{ ($order->ordered_at ?: $order->created_at)->format('H:i') }}</div>
                                </td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order->saweria_id) }}">
                                        {{ $order->product_name ?: 'Menunggu detail' }}
                                    </a>
                                    @if ($order->game_name)
                                        <div class="ad-muted">{{ $order->game_name }}</div>
                                    @endif
                                </td>
                                <td>
                                    {{ $order->donator_name ?: '-' }}
                                    @if ($order->donator_email)
                                        <div class="ad-muted ad-mono">{{ $order->donator_email }}</div>
                                    @endif
                                </td>
                                <td class="ad-muted">
                                    {{ $order->source_label }}
                                    @if ($order->attribution)
                                        <div class="ad-muted" style="font-size:11px">yakin: {{ $order->attribution }}</div>
                                    @endif
                                </td>
                                <td><span class="ad-badge {{ $order->state }}">{{ $order->state_label }}</span></td>
                                <td class="num">{{ $rp($order->amount_raw) }}</td>
                                <td class="num">{{ $order->fee ? $rp($order->fee) : '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="ad-pag">{{ $orders->links('admin.pagination') }}</div>
    @endif
@endsection

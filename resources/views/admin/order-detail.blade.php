@extends('admin.layout')
@section('title', 'Detail pesanan')

@php
    $rp = fn ($n) => $n === null ? '-' : 'Rp' . number_format((int) $n, 0, ',', '.');
@endphp

@section('content')
    <div class="ad-head">
        <div>
            <h1>{{ $order->product_name ?: 'Pesanan menunggu detail' }}</h1>
            <p>{{ $order->game_name ?: 'Nama produk belum terambil dari Saweria' }}</p>
        </div>
        <div style="display:flex; gap:8px; flex-wrap:wrap">
            <a href="{{ route('admin.orders') }}" class="ad-btn ghost">&larr; Kembali</a>
            <form method="post" action="{{ route('admin.orders.refresh', $order->saweria_id) }}">
                @csrf
                <button type="submit" class="ad-btn">Segarkan dari Saweria</button>
            </form>
        </div>
    </div>

    <div class="ad-grid">
        <div class="ad-panel">
            <h2>Pesanan</h2>
            <p class="hint">Status berasal dari Saweria, bukan dihitung sendiri.</p>

            <dl class="ad-kv">
                <dt>Status</dt>
                <dd><span class="ad-badge {{ $order->state }}">{{ $order->state_label }}</span></dd>

                <dt>Track ID</dt>
                <dd class="ad-mono">{{ $order->saweria_id }}</dd>

                <dt>Pembayaran</dt>
                <dd>{{ $order->payment_status ?: '-' }} &middot; {{ $order->payment_method ?: '-' }}</dd>

                <dt>Pengiriman</dt>
                <dd>{{ $order->fulfillment_status ?: '-' }}</dd>

                <dt>Harga produk</dt>
                <dd>{{ $rp($order->product_price) }}</dd>

                <dt>Biaya pembayaran</dt>
                <dd>{{ $rp($order->fee) }}</dd>

                <dt>Dibayar pembeli</dt>
                <dd><strong>{{ $rp($order->amount_raw) }}</strong> {{ $order->currency }}</dd>

                <dt>Dipesan</dt>
                <dd>{{ $order->ordered_at?->translatedFormat('d M Y, H:i') ?: '-' }}</dd>

                <dt>Dibayar</dt>
                <dd>{{ $order->paid_at?->translatedFormat('d M Y, H:i') ?: '-' }}</dd>

                <dt>Detail terambil</dt>
                <dd>
                    @if ($order->enriched_at)
                        {{ $order->enriched_at->translatedFormat('d M Y, H:i') }}
                    @else
                        <span class="ad-muted">belum ({{ $order->enrich_attempts }} percobaan)</span>
                    @endif
                </dd>
            </dl>
        </div>

        <div class="ad-panel">
            <h2>Pembeli</h2>
            <p class="hint">Data ini dikirim Saweria bersama callback.</p>

            <dl class="ad-kv">
                <dt>Nama</dt>
                <dd>{{ $order->donator_name ?: '-' }}</dd>

                <dt>Email</dt>
                <dd class="ad-mono">{{ $order->donator_email ?: '-' }}</dd>

                <dt>Pesan</dt>
                <dd>{{ $order->message ?: '-' }}</dd>
            </dl>

            <h2 style="margin-top:24px">Sumber trafik</h2>
            <p class="hint">
                Sambungan ke kunjungan bersifat perkiraan: pembayaran terjadi di Saweria
                dan callback-nya tidak membawa penanda sesi kita.
            </p>

            <dl class="ad-kv">
                <dt>Sumber</dt>
                <dd>{{ $order->source_label }}</dd>

                <dt>Tingkat keyakinan</dt>
                <dd>{{ $order->attribution ?: 'tidak terhubung' }}</dd>

                @if ($order->utm_campaign)
                    <dt>Kampanye</dt>
                    <dd>{{ $order->utm_campaign }}</dd>
                @endif
            </dl>
        </div>
    </div>

    <div class="ad-panel">
        <h2>Perjalanan pengunjung</h2>
        <p class="hint">
            @if ($journey->isEmpty())
                Pesanan ini tidak berhasil dihubungkan dengan kunjungan mana pun.
            @else
                {{ $journey->count() }} langkah tercatat pada kunjungan yang menghasilkan pesanan ini.
            @endif
        </p>

        @if ($journey->isNotEmpty())
            <ul class="ad-journey" style="padding-left:4px; margin:0">
                @foreach ($journey as $event)
                    <li>
                        <strong>{{ $event->label }}</strong>
                        <span class="ad-muted">&middot; {{ $event->occurred_at->translatedFormat('d M, H:i:s') }}</span>
                        @if ($event->path)
                            <div class="ad-muted ad-mono">/{{ $event->path }}</div>
                        @endif
                        @if ($event->product_slug)
                            <div class="ad-muted">paket: {{ $event->product_slug }}</div>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endsection

@extends('welcome')
@section('title', 'Lacak Pesanan')
@push('css')
<link rel="stylesheet" href="{{ asset('frontend/css/order-tracking.css') }}">
@endpush
@section('content')
<main class="lu-tracking container">
    <header class="lu-tracking-intro">
        <p class="lu-tracking-eyebrow">LevelUp Market · Bantuan pesanan</p>
        <h1>Lacak Pesanan Top Up &amp; Voucher</h1>
        <p>Cek status pembayaran dan pengiriman dengan Track ID yang kamu dapatkan dari Saweria.</p>
    </header>
    <section class="lu-tracking-panel" aria-labelledby="tracking-form-title">
        <h2 id="tracking-form-title">Masukkan Track ID</h2>
        <form method="post" action="{{ route('orders.lookup') }}" id="tracking-form" autocomplete="off">
            @csrf
            <label for="track-id">Track ID / nomor invoice Saweria</label>
            <div class="lu-tracking-input-row">
                <input id="track-id" name="track_id" value="{{ $trackId }}" type="text" required maxlength="36" spellcheck="false" autocapitalize="off" aria-describedby="track-id-help" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx">
                <button type="submit" id="tracking-submit">{{ $order ? 'Perbarui status' : 'Lacak Pesanan' }}</button>
            </div>
            <p id="track-id-help" class="lu-tracking-muted">Salin ID dari bukti pembayaran atau halaman pelacakan Saweria. Simpan ID ini untuk dirimu sendiri.</p>
        </form>
        @if ($error)<p class="lu-tracking-error" role="alert">{{ $error }}</p>@endif
    </section>
    @if ($order)
        @php
            $money = static fn ($amount) => $amount === null ? 'Belum tersedia' : ($order['currency'] === 'IDR' ? 'Rp ' : $order['currency'].' ') . number_format($amount, $order['currency'] === 'IDR' ? 0 : 2, ',', '.');
            $stateLabels = ['refunded' => 'Pembayaran dikembalikan', 'pending' => 'Menunggu pembayaran', 'processing' => 'Pesanan diproses', 'completed' => 'Pesanan selesai', 'failed' => 'Pesanan gagal', 'expired' => 'Pesanan kedaluwarsa', 'unknown' => 'Status belum dapat dipastikan'];
        @endphp
        <section id="tracking-result" class="lu-tracking-result" data-track-id="{{ $order['id'] }}" data-state="{{ $order['state'] }}" aria-labelledby="tracking-status-title">
            <div class="lu-tracking-panel">
                <p class="lu-tracking-eyebrow">Status dari Saweria</p>
                <h2 id="tracking-status-title">{{ $stateLabels[$order['state']] ?? $stateLabels['unknown'] }}</h2>
                <p class="lu-tracking-muted" id="tracking-poll-status" role="status" aria-live="polite">{{ in_array($order['state'], ['completed', 'failed', 'expired', 'refunded']) ? 'Status akhir diterima. Pembaruan otomatis berhenti.' : 'Status diperbarui otomatis setiap 4 detik selama halaman aktif.' }}</p>
                <ol class="lu-tracking-steps" aria-label="Tahap pesanan">
                    @foreach (['Pembayaran', 'Diproses', 'Selesai'] as $label)
                        <li data-step="{{ $loop->iteration }}" class="{{ $order['step'] >= $loop->iteration ? 'is-done' : '' }}"><span aria-hidden="true">{{ $order['step'] >= $loop->iteration ? '✓' : $loop->iteration }}</span>{{ $label }}</li>
                    @endforeach
                </ol>
                <p class="lu-tracking-id">Track ID <code id="order-id">{{ $order['id'] }}</code></p>
            </div>
            <div class="lu-tracking-grid">
                <section class="lu-tracking-panel" aria-labelledby="order-product-title">
                    <h2 id="order-product-title">Detail pesanan</h2>
                    <div class="lu-tracking-product">
                        @if ($order['cover'])<img src="{{ $order['cover'] }}" alt="Cover {{ $order['game_name'] }}" title="{{ $order['game_name'] }}" width="88" height="88" referrerpolicy="no-referrer">@endif
                        <div><h3 id="order-product-name">{{ $order['product_name'] }}</h3><p id="order-game-name">{{ $order['game_name'] }}</p></div>
                    </div>
                    <dl class="lu-tracking-details">
                        <div><dt>Status pembayaran</dt><dd id="order-payment-status">{{ $order['payment_status'] }}</dd></div>
                        <div><dt>Status pengiriman</dt><dd id="order-fulfillment-status">{{ $order['fulfillment_status'] }}</dd></div>
                        <div><dt>Dibuat pada</dt><dd id="order-created-at">{{ $order['created_at'] ?: 'Belum tersedia' }}</dd></div>
                        <div><dt>Waktu pembayaran</dt><dd id="order-paid-at">{{ $order['paid_at'] ?: 'Belum tersedia' }}</dd></div>
                    </dl>
                </section>
                <section class="lu-tracking-panel" aria-labelledby="order-payment-title">
                    <h2 id="order-payment-title">Rincian pembayaran</h2>
                    <dl class="lu-tracking-details">
                        <div><dt>Metode pembayaran</dt><dd id="order-payment-method">{{ $order['payment_method'] }}</dd></div>
                        <div><dt>Harga produk</dt><dd id="order-price">{{ $money($order['product_price']) }}</dd></div>
                        <div><dt>Biaya pembayaran</dt><dd id="order-fee">{{ $money($order['fee']) }}</dd></div>
                        <div class="lu-tracking-total"><dt>Total pembayaran</dt><dd id="order-total">{{ $money($order['total']) }}</dd></div>
                    </dl>
                    <p class="lu-tracking-muted">Rincian mengikuti data Saweria. Waktu ditampilkan dalam WIB.</p>
                </section>
            </div>
        </section>
    @endif
    <section class="lu-tracking-help">
        <h2>Belum menemukan Track ID?</h2>
        <p>Periksa bukti transaksi atau halaman pelacakan Saweria setelah checkout. Pastikan ID disalin lengkap, termasuk tanda hubungnya.</p>
        <a href="/contact" title="Hubungi LevelUp Market untuk bantuan pesanan">Butuh bantuan pesanan? Hubungi kami &rarr;</a>
    </section>
</main>
@endsection
@push('script')
<script src="{{ asset('frontend/js/order-tracking.js') }}" defer></script>
@endpush

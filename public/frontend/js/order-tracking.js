(() => {
    'use strict';
    const result = document.getElementById('tracking-result');
    const form = document.getElementById('tracking-form');
    if (!result || !form) return;
    const id = result.dataset.trackId;
    const terminal = new Set(['completed', 'failed', 'expired', 'refunded']);
    const labels = { refunded: 'Pembayaran dikembalikan', pending: 'Menunggu pembayaran', processing: 'Pesanan diproses', completed: 'Pesanan selesai', failed: 'Pesanan gagal', expired: 'Pesanan kedaluwarsa', unknown: 'Status belum dapat dipastikan' };
    const notice = document.getElementById('tracking-poll-status');
    let state = result.dataset.state, timer, controller, failures = 0, stopped = terminal.has(state);
    const started = Date.now();
    const set = (id, value) => { document.getElementById(id).textContent = value || 'Belum tersedia'; };
    const schedule = delay => { clearTimeout(timer); if (!stopped && !document.hidden) timer = setTimeout(poll, delay); };
    const update = order => {
        state = order.state; result.dataset.state = state;
        set('tracking-status-title', labels[state] || labels.unknown);
        set('order-product-name', order.product_name); set('order-game-name', order.game_name);
        set('order-payment-status', order.payment_status); set('order-fulfillment-status', order.fulfillment_status);
        set('order-created-at', order.created_at); set('order-paid-at', order.paid_at); set('order-payment-method', order.payment_method);
        const money = value => value === null ? 'Belum tersedia' : new Intl.NumberFormat('id-ID', { style: 'currency', currency: order.currency, maximumFractionDigits: order.currency === 'IDR' ? 0 : 2 }).format(value);
        set('order-price', money(order.product_price)); set('order-fee', money(order.fee)); set('order-total', money(order.total));
        result.querySelectorAll('[data-step]').forEach(step => { const done = Number(step.dataset.step) <= order.step; step.classList.toggle('is-done', done); step.querySelector('span').textContent = done ? '✓' : step.dataset.step; });
        stopped = terminal.has(state);
        notice.textContent = stopped ? 'Status akhir diterima. Pembaruan otomatis berhenti.' : 'Status terbaru diterima. Diperbarui otomatis setiap 4 detik.';
    };
    async function poll() {
        if (stopped || document.hidden || controller) return;
        if (Date.now() - started >= 10 * 60 * 1000) { stopped = true; notice.textContent = 'Pembaruan otomatis dijeda setelah 10 menit. Gunakan Perbarui status untuk mengecek kembali.'; return; }
        controller = new AbortController();
        const timeout = setTimeout(() => controller?.abort(), 12000);
        try {
            const response = await fetch(form.action, {
                method: 'POST', credentials: 'same-origin', cache: 'no-store', signal: controller.signal,
                headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ track_id: id })
            });
            if ([419, 422, 404].includes(response.status)) { stopped = true; notice.textContent = 'Pembaruan otomatis berhenti. Muat ulang halaman dan periksa kembali Track ID.'; return; }
            if (!response.ok) throw new Error('temporary_failure');
            const data = await response.json();
            if (!data.order || data.order.id !== id) throw new Error('invalid_response');
            update(data.order); failures = 0;
        } catch (_) {
            if (!document.hidden) { failures++; notice.textContent = 'Status terbaru belum tersedia. Mencoba kembali; rincian sebelumnya tetap ditampilkan.'; }
        } finally {
            clearTimeout(timeout); controller = null;
            schedule(failures ? Math.min(60000, 10000 * 2 ** Math.min(failures - 1, 3)) : 4000);
        }
    }
    form.addEventListener('submit', () => { stopped = true; clearTimeout(timer); controller?.abort(); });
    document.addEventListener('visibilitychange', () => { clearTimeout(timer); if (document.hidden) { controller?.abort(); if (!stopped) notice.textContent = 'Pembaruan dijeda selama tab tidak aktif.'; } else schedule(1000); });
    window.addEventListener('pagehide', () => { stopped = true; clearTimeout(timer); controller?.abort(); });
    window.addEventListener('pageshow', event => {
        if (event.persisted && !terminal.has(state)) { stopped = false; schedule(1000); }
    });
    schedule(4000);
})();

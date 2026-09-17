(() => {
    'use strict';
    const config = window.levelupAnalyticsConfig || {};
    const enabled = config.enabled && typeof window.gtag === 'function';
    const emit = (name, params = {}) => {
        if (!enabled) return;
        window.gtag('event', name, { ...params, send_to: config.measurementId, debug_mode: !!config.debug });
    };
    const readItem = element => {
        try { return JSON.parse(element.dataset.analyticsItem || '{}'); } catch (_) { return {}; }
    };
    const navigateWithEvent = (event, link, name, params) => {
        if (!enabled || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || link.target === '_blank') {
            emit(name, params);
            return;
        }
        event.preventDefault();
        let navigated = false;
        const go = () => { if (!navigated) { navigated = true; location.assign(link.href); } };
        emit(name, { ...params, transport_type: 'beacon', event_callback: go, event_timeout: 350 });
        setTimeout(go, 400);
    };
    const productPage = document.querySelector('[data-analytics-product]');
    const group = productPage ? readItem(productPage) : null;
    const packageItem = option => ({
        item_id: group?.item_id,
        item_name: group?.item_name,
        saweria_product_id: option.dataset.productId,
        item_brand: group?.item_brand || 'LevelUp Market',
        item_category: group?.item_category || '',
        item_category2: option.dataset.productCategory || '',
        item_variant: option.dataset.productName,
        price: Number(option.dataset.productPrice), quantity: 1,
        affiliation: 'LevelUp Market / Saweria'
    });
    if (group) emit('view_item', { items: [group] });
    if (config.successEvent) {
        emit(config.successEvent, { form_id: config.successEvent === 'generate_lead' ? 'contact' : 'review' });
    }
    // List impressions represent visible cards, not the full hidden catalog.
    const seen = new Set();
    const observer = 'IntersectionObserver' in window ? new IntersectionObserver(entries => {
        const lists = new Map();
        entries.forEach(entry => {
            const card = entry.target;
            if (!entry.isIntersecting || card.hidden) return;
            const item = readItem(card);
            const list = card.dataset.analyticsList || (card.closest('#katalog') ? 'catalog' : 'popular');
            const key = list + ':' + item.item_id;
            if (!item.item_id || seen.has(key)) return;
            seen.add(key);
            if (!lists.has(list)) lists.set(list, []);
            lists.get(list).push(item);
        });
        lists.forEach((items, list) => emit('view_item_list', { item_list_id: list, item_list_name: list, items }));
    }, { threshold: 0.5 }) : null;
    document.querySelectorAll('.lu-card').forEach(card => observer?.observe(card));
    document.addEventListener('click', event => {
        const itemLink = event.target.closest('a[data-analytics-item]');
        if (itemLink) {
            const list = itemLink.dataset.analyticsList || (itemLink.closest('#katalog') ? 'catalog' : 'popular');
            navigateWithEvent(event, itemLink, 'select_item', { item_list_id: list, item_list_name: list, items: [readItem(itemLink)] });
        }
        const option = event.target.closest('.lu-product-option');
        if (option) {
            const item = packageItem(option);
            emit('select_item', { item_list_id: 'denominations', item_list_name: group?.item_name, currency: 'IDR', items: [item] });
            // Modifier-click on a package follows the no-JS link directly.
            if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) {
                emit('begin_checkout', { currency: 'IDR', value: item.price, checkout_provider: 'saweria', items: [item] });
            }
        }
        const checkout = event.target.closest('#saweria-checkout, #mobile-saweria-checkout');
        if (checkout?.getAttribute('href')) {
            const selected = document.querySelector('.lu-product-option[aria-pressed="true"]');
            if (!selected) return;
            const item = packageItem(selected);
            const params = { currency: 'IDR', value: item.price, checkout_provider: 'saweria', items: [item] };
            navigateWithEvent(event, checkout, 'begin_checkout', params);
        }
        const filter = event.target.closest('.lu-tab[data-filter]');
        if (filter) emit('catalog_filter', { category: filter.dataset.filter });
        if (event.target.closest('#catalog-more')) emit('catalog_load_more');
        const social = event.target.closest('.footer-social-list a, a[href^="https://wa.me/"]');
        if (social) {
            const host = new URL(social.href).hostname;
            const network = host.includes('instagram') ? 'instagram' : host.includes('facebook') ? 'facebook' : 'whatsapp';
            emit('contact_click', { contact_method: network });
        }
        const contact = event.target.closest('a[href^="mailto:"], a[href^="tel:"]');
        if (contact) emit('contact_click', { contact_method: contact.href.startsWith('mailto:') ? 'email' : 'phone' });
    });
    // Never send free text typed by visitors. Search labels use matched catalog names only.
    let searchTimer;
    const search = (source, results) => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => emit('catalog_search', {
            matched_items: results.length ? results.slice(0, 2).map(item => item.name).join(' | ').slice(0, 100) : 'no_catalog_match',
            search_source: source, result_count: results.length
        }), 500);
    };
    document.addEventListener('levelup:search', event => search('header', event.detail.results || []));
    const catalogSearch = document.getElementById('catalog-search');
    let catalogDelay;
    catalogSearch?.addEventListener('input', () => {
        clearTimeout(catalogDelay);
        catalogDelay = setTimeout(() => {
            if (!catalogSearch.value.trim()) return;
            const results = [...document.querySelectorAll('#catalog-grid .lu-card:not([hidden])')]
                .map(card => ({ name: readItem(card).item_name }));
            search('catalog', results);
        }, 250);
    });

    /*
     * Section statistik hero.
     *
     * Tombol top up di section ini adalah pintu masuk pembelian tersendiri,
     * jadi dicatat sebagai promosi GA4: view_promotion saat tabelnya benar
     * benar terlihat, select_promotion saat tombolnya ditekan. Tanpa ini,
     * GA4 tidak bisa memisahkan pembeli yang datang dari tabel meta dengan
     * yang datang dari katalog biasa.
     */
    const statBoxes = [...document.querySelectorAll('[data-game-stats]')];

    const promotionOf = box => ({
        promotion_id: box.id || 'game-stats',
        promotion_name: 'Hero Meta Terkini',
        creative_name: box.querySelector('h3')?.textContent.trim() || '',
        creative_slot: 'homepage_hero_meta'
    });

    if (statBoxes.length && 'IntersectionObserver' in window) {
        const seenBoxes = new WeakSet();
        const boxObserver = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (!entry.isIntersecting || seenBoxes.has(entry.target)) return;
                seenBoxes.add(entry.target);
                emit('view_promotion', promotionOf(entry.target));
            });
        // Ambang dibuat longgar: kotaknya tinggi, dan pada guliran cepat
        // peramban tidak selalu sempat mencatat keadaan saat isinya terlihat
        // penuh. Seperempat bagian sudah cukup untuk menyatakan terlihat.
        }, { threshold: 0.25 });

        statBoxes.forEach(box => boxObserver.observe(box));
    }

    document.addEventListener('click', event => {
        const cta = event.target.closest('.lu-gs-cta');
        if (cta) {
            const box = cta.closest('[data-game-stats]');
            navigateWithEvent(event, cta, 'select_promotion', box ? promotionOf(box) : {});
            return;
        }

        // Pindah halaman tabel menandakan orang menelusuri lebih jauh,
        // bukan sekadar melihat sepuluh baris teratas.
        const pager = event.target.closest('[data-gs-next], [data-gs-prev], [data-rank-next], [data-rank-prev]');
        if (pager) {
            const box = pager.closest('[data-game-stats], .lu-rank');
            emit('hero_table_page', {
                game: box?.querySelector('h3, h2')?.textContent.trim() || '',
                direction: pager.matches('[data-gs-next], [data-rank-next]') ? 'next' : 'prev'
            });
        }
    });
})();

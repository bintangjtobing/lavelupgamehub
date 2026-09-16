(() => {
    const checkout = document.getElementById('saweria-checkout');
    if (!checkout) return;
    const options = [...document.querySelectorAll('.lu-product-option')];
    const currency = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 });
    options.forEach(option => {
        option.setAttribute('role', 'button');
        option.setAttribute('aria-pressed', 'false');
        option.addEventListener('click', event => {
            if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
            event.preventDefault();
            options.forEach(other => other.setAttribute('aria-pressed', String(other === option)));
            document.getElementById('selected-name').textContent = option.dataset.productName;
            document.getElementById('selected-price').textContent = currency.format(Number(option.dataset.productPrice));
            checkout.href = option.href;
            checkout.removeAttribute('aria-disabled');
            checkout.removeAttribute('tabindex');
            document.getElementById('mobile-selected-name').textContent = option.dataset.productName;
            document.getElementById('mobile-selected-price').textContent = currency.format(Number(option.dataset.productPrice));
            document.getElementById('mobile-saweria-checkout').href = option.href;
            document.getElementById('mobile-checkout').hidden = false;
        });
        option.addEventListener('keydown', event => {
            if (event.key === ' ') { event.preventDefault(); option.click(); }
        });
    });
})();

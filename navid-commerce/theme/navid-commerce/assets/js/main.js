/* Native, progressively enhanced navigation and product previews. */
(() => {
  'use strict';
  document.documentElement.classList.add('nc-js');
  const refreshCartLabels = () => {
    document.querySelectorAll('.cart-link[data-cart-label]').forEach((link) => {
      const count = link.querySelector('.nc-cart-count');
      if (count) link.setAttribute('aria-label', link.dataset.cartLabel.replace('%s', count.textContent));
    });
  };
  const refreshCartCount = () => {
    const state = document.querySelector('.nc-cart-state[data-count]');
    if (state) document.querySelectorAll('.nc-cart-count').forEach((node) => {
      node.textContent = state.getAttribute('data-count');
    });
    refreshCartLabels();
  };
  refreshCartCount();
  if (typeof window.jQuery === 'function') {
    window.jQuery(document.body).on('updated_wc_div.navidCommerce', () => queueMicrotask(refreshCartCount));
    window.jQuery(document.body).on('added_to_cart.navidCommerce removed_from_cart.navidCommerce wc_fragments_refreshed.navidCommerce', () => queueMicrotask(refreshCartLabels));
  }
  const toggle = document.querySelector('.menu-toggle');
  const nav = document.querySelector('#site-navigation');
  const categories = document.querySelector('.category-menu');
  const setOpen = (open, focus = false) => {
    if (!toggle || !nav) return;
    toggle.setAttribute('aria-expanded', String(open));
    nav.classList.toggle('is-open', open);
    if (focus) toggle.focus();
  };
  toggle?.addEventListener('click', () => {
    const open = toggle.getAttribute('aria-expanded') !== 'true';
    if (open && categories) categories.open = false;
    setOpen(open);
  });
  categories?.addEventListener('toggle', () => { if (categories.open) setOpen(false); });
  document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') return;
    if (toggle?.getAttribute('aria-expanded') === 'true') setOpen(false, true);
    if (categories?.open) {
      categories.open = false;
      categories.querySelector('summary')?.focus();
    }
  });
  document.addEventListener('click', (event) => {
    if (nav && toggle && !nav.contains(event.target) && !toggle.contains(event.target)) setOpen(false);
    if (categories && !categories.contains(event.target)) categories.open = false;
  });
  nav?.addEventListener('click', (event) => { if (event.target.closest('a')) setOpen(false); });
  const dialog = document.querySelector('#nc-product-preview');
  if (!dialog || typeof dialog.showModal !== 'function') return;
  document.documentElement.classList.add('nc-preview-ready');
  let trigger;
  document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-product-preview]');
    if (!button) return;
    let data;
    try { data = JSON.parse(button.dataset.productPreview); } catch { return; }
    trigger = button;
    dialog.querySelector('#nc-preview-title').textContent = data.name;
    dialog.querySelector('.nc-preview-image').src = data.image;
    dialog.querySelector('.nc-preview-image').alt = data.name;
    dialog.querySelector('.nc-preview-price').textContent = data.price;
    const regular = dialog.querySelector('.nc-preview-regular');
    regular.textContent = data.regular;
    regular.hidden = !data.regular;
    dialog.querySelector('.nc-preview-description').textContent = data.description;
    const stock = dialog.querySelector('.nc-preview-stock');
    stock.textContent = data.stock;
    stock.classList.toggle('is-unavailable', !data.inStock);
    dialog.querySelector('.nc-preview-link').href = data.url;
    dialog.showModal();
    document.documentElement.classList.add('nc-dialog-open');
    dialog.querySelector('.nc-preview-close').focus();
  });
  dialog.querySelector('.nc-preview-close').addEventListener('click', () => dialog.close());
  dialog.addEventListener('click', (event) => { if (event.target === dialog) dialog.close(); });
  dialog.addEventListener('close', () => {
    document.documentElement.classList.remove('nc-dialog-open');
    if (trigger?.isConnected) trigger.focus();
  });
})();

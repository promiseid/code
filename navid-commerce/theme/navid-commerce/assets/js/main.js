/* Small progressive enhancement; navigation works without JavaScript. */
(() => {
  'use strict';
  const refreshCartLabels = () => {
    document.querySelectorAll('.cart-link[data-cart-label]').forEach((link) => {
      const count = link.querySelector('.nc-cart-count');
      if (count) link.setAttribute('aria-label', link.dataset.cartLabel.replace('%s', count.textContent));
    });
  };
  // Read the count from the cart HTML WooCommerce already refreshed.
  const refreshCartCount = () => {
    const state = document.querySelector('.nc-cart-state[data-count]');
    if (state) document.querySelectorAll('.nc-cart-count').forEach((node) => {
        node.textContent = state.getAttribute('data-count');
      });
    refreshCartLabels();
  };
  refreshCartCount();
  if (typeof window.jQuery === 'function') {
    window.jQuery(document.body).on('updated_wc_div.navidCommerce', refreshCartCount);
    window.jQuery(document.body).on('added_to_cart.navidCommerce removed_from_cart.navidCommerce wc_fragments_refreshed.navidCommerce', refreshCartLabels);
  }
  const toggle = document.querySelector('.menu-toggle');
  const nav = document.querySelector('#site-navigation');
  if (!toggle || !nav) return;
  document.documentElement.classList.add('nc-js');
  const setOpen = (open, focus = false) => {
    toggle.setAttribute('aria-expanded', String(open));
    nav.classList.toggle('is-open', open);
    if (focus) toggle.focus();
  };
  toggle.addEventListener('click', () => setOpen(toggle.getAttribute('aria-expanded') !== 'true'));
  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') setOpen(false, true);
  });
  document.addEventListener('click', (event) => {
    if (!nav.contains(event.target) && !toggle.contains(event.target)) setOpen(false);
  });
  nav.addEventListener('click', (event) => {
    if (event.target.closest('a')) setOpen(false);
  });
})();

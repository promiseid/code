# NavidShop v0 visual workspace

This standalone Next.js preview exists to let v0 edit and preview the Persian storefront UI without changing the PHP WordPress theme.

Run: npm install && npm run dev
Build check: npm run build

Import repository promiseid/code in v0. Select branch feat/v0-design-bridge and root directory navid-commerce/v0-preview when available. The actual WordPress/WooCommerce code stays in navid-commerce/theme/navid-commerce and associated plugins.

IMPORTANT: All products, prices, cart and favorites in this Next.js preview are mock state. No checkout, real WooCommerce API, real authentication, server storage, or WordPress admin is integrated. Port reviewed UI to PHP/WooCommerce templates separately; do not deploy this mock as a storefront or market it as ready for payment.

Design requirements:
- RTL Persian copy, responsive layouts, accessible controls, graceful reduced motion.
- High-end original e-commerce style, inspired by interaction patterns but not copying DigiKala branding.
- Use controlled, optional Three.js effects only when they don't harm LCP, INP or mobile performance.
- Single-source design tokens, efficient styles, lazy imagery; retain semantic and WCAG-friendly markup.
- Do not modify, overwrite, or replace PHP theme / WordPress admin files unless explicitly requested.

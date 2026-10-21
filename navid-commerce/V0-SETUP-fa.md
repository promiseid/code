# آماده‌سازی v0 برای ادامه نویدشاپ

**هدف:** توسعه طراحی ظاهری همان قالب WooCommerce نویدشاپ ۰٫۴٫۰ در یک فضای بصری جدا؛ نه جایگزینی وردپرس با Next.js.

## اتصال به v0
1. در https://v0.app با حساب Vercel خود وارد شوید.
2. از دکمه + گزینه Import from GitHub را انتخاب کنید.
3. مخزن promiseid/code را انتخاب کنید. اگر نمایش داده نشد، دسترسی Vercel GitHub App به این مخزن را بررسی کنید.
4. شاخه feat/v0-design-bridge را به عنوان شاخه پایه انتخاب کنید.
5. پوشه پروژه را navid-commerce/v0-preview تعیین کنید (در صورت نمایش گزینه Root Directory).
6. پیش‌نمایش را اجرا کنید و دستور زیر را در گفت‌وگوی v0 بنویسید.

## دستور پیشنهادی v0
You are improving an existing Persian RTL e-commerce visual prototype for NavidShop.
Source of truth: the existing native PHP WooCommerce theme under navid-commerce/theme/navid-commerce.
The folder navid-commerce/v0-preview is ONLY a Next.js visual design lab. Do not replace the real WordPress site with a headless storefront or claim mock checkout is functional.

Upgrade the prototype into a premium, unique, high-fidelity Persian marketplace experience: DigiKala-class usability without copying its logo, copyrighted assets, exact visual identity or proprietary content. Design fully responsive desktop/tablet/mobile storefront, product listings with filters, product detail gallery with optional 3D view, mini-cart, mobile navigation, search suggestion overlay, branded account views, wishlist/compare, campaign banners and a polished admin options UI mock.

Honor Persian RTL, Vazirmatn-compatible typography, WCAG accessibility, semantic HTML, reduced-motion fallback, font/image optimization, core web vitals and centralized CSS variables/design tokens. Use tasteful micro-interactions and optional lazy-loaded Three.js for selected products only. All mock data must be visibly labeled; never invent real payment, auth or WooCommerce API calls.

Iterate on the Next preview first; keep each component easy to port into secure WordPress PHP templates and native WooCommerce hooks. Propose a PHP/WooCommerce migration mapping as a final deliverable. All WordPress business logic, authentication, pricing, payment and inventory remain on the WooCommerce server. Do not publish or merge without explicit review.

## وضعیت
- شاخه اصلی main و نسخه نصب‌شدنی PHP دست‌نخورده می‌ماند.
- این یک محیط طراحی است، نه فروشگاه عملیاتی و نه نصب افزونه v0 روی ویندوز.
- v0 در حساب کاربر نیاز به ورود و تأیید دسترسی GitHub دارد.
- بعد از تأیید خروجی v0، تغییرات بصری به قالب واقعی وردپرس انتقال و در WordPress Playground تست خواهند شد.

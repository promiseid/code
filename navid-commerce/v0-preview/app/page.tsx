"use client";

import { useMemo, useState } from "react";

type Product = { id: number; name: string; category: string; price: string; oldPrice?: string; badge?: string; art: string; tone: string };
const categories = [
  { label: "همه محصولات", icon: "✦" },
  { label: "موبایل", icon: "◈" },
  { label: "لپ‌تاپ", icon: "▣" },
  { label: "صوتی", icon: "♫" },
  { label: "گجت", icon: "⌚" },
];
const products: Product[] = [
  { id: 1, name: "هدفون بی‌سیم سری Studio", category: "صوتی", price: "۴٬۸۹۰٬۰۰۰", oldPrice: "۶٬۱۲۰٬۰۰۰", badge: "۲۰٪ تخفیف", art: "🎧", tone: "lilac" },
  { id: 2, name: "ساعت هوشمند Pulse", category: "گجت", price: "۳٬۱۵۰٬۰۰۰", oldPrice: "۳٬۸۹۰٬۰۰۰", badge: "پرفروش", art: "⌚", tone: "mint" },
  { id: 3, name: "گوشی هوشمند مدل Nova", category: "موبایل", price: "۲۸٬۴۹۰٬۰۰۰", badge: "جدید", art: "📱", tone: "blue" },
  { id: 4, name: "لپ‌تاپ سبک Ultra", category: "لپ‌تاپ", price: "۵۹٬۹۰۰٬۰۰۰", oldPrice: "۶۴٬۹۰۰٬۰۰۰", art: "💻", tone: "peach" },
];

export default function HomePage() {
  const [query, setQuery] = useState("");
  const [category, setCategory] = useState("همه محصولات");
  const [cartCount, setCartCount] = useState(0);
  const [liked, setLiked] = useState<number[]>([]);
  const visible = useMemo(
    () => products.filter((p) => (category === "همه محصولات" || p.category === category) && p.name.includes(query.trim())),
    [query, category]
  );
  const toggleLiked = (id: number) => setLiked((prev) => prev.includes(id) ? prev.filter((x) => x !== id) : [...prev, id]);

  return (
    <main>
      <div className="topline"><span>ارسال سریع، تجربه‌ای متفاوت</span><span>طراحی آزمایشی — اتصال به ووکامرس در مرحله بعد</span></div>
      <header className="site-header">
        <div className="shell nav">
          <a className="brand" href="#" aria-label="صفحه نخست نویدشاپ"><span className="brand-mark">N</span><span>نوید<span className="brand-accent">شاپ</span><small>SMARTER CHOICES</small></span></a>
          <label className="search" aria-label="جست‌وجوی محصول"><span aria-hidden="true">⌕</span><input value={query} onChange={(event) => setQuery(event.target.value)} placeholder="دنبال چی می‌گردی؟" /></label>
          <div className="header-actions"><a className="header-link" href="#products">محصولات</a><span className="header-link favorite-count" aria-label="تعداد علاقه‌مندی‌ها">♡ {liked.length.toLocaleString("fa-IR")}</span><button className="cart" type="button" aria-label={"سبد خرید آزمایشی، " + cartCount + " کالا"}>سبد خرید <b>{cartCount.toLocaleString("fa-IR")}</b></button></div>
        </div>
        <div className="shell subnav"><a href="#categories">دسته‌بندی‌ها</a><a href="#products">پیشنهادهای ویژه</a><a href="#services">خدمات نویدشاپ</a><span>امکان خرید واقعی در این پیش‌نمایش غیرفعال است</span></div>
      </header>

      <section className="shell hero" aria-label="کمپین اصلی">
        <div className="hero-content"><span className="hero-kicker"><i /> مجموعه جدید نویدشاپ ۲۰۲۶</span><h1>تکنولوژی،<br /><em>به سبک تو.</em></h1><p>از انتخاب‌های هوشمند تا جزئیات دقیق؛ یک تجربه خرید مدرن، سریع و چشم‌نواز.</p><a className="hero-btn" href="#products">کشف پیشنهادها <span aria-hidden="true">←</span></a><div className="hero-foot"><strong>۰۱ <span>/ ۰۳</span></strong><span className="hero-line" /></div></div>
        <div className="hero-art" aria-hidden="true"><div className="orbit orbit-a" /><div className="orbit orbit-b" /><div className="halo" /><div className="device"><div className="device-screen"><span className="device-dot" /><span className="device-clock">۱۰:۰۸</span><span className="device-weather">◌ ۲۴°</span></div><div className="device-shadow" /></div><div className="floating-tag tag-a">DESIGNED TO INSPIRE</div><div className="floating-tag tag-b">N / 2026</div></div>
      </section>

      <section className="shell service-strip" id="services" aria-label="مزایای فروشگاه"><div><b>✧</b><span><strong>انتخاب مطمئن</strong><small>جزئیات شفاف محصولات</small></span></div><div><b>↗</b><span><strong>ارسال قابل پیگیری</strong><small>با روش دلخواه شما</small></span></div><div><b>♢</b><span><strong>تنوع بیشتر</strong><small>برای هر سلیقه‌ای</small></span></div><div><b>◎</b><span><strong>پشتیبانی همراه</strong><small>از انتخاب تا دریافت</small></span></div></section>

      <section className="shell section" id="categories"><div className="section-head"><div><span className="eyebrow">EXPLORE BY CATEGORY</span><h2>دنیای تو، انتخاب تو</h2></div><span className="muted">دسته‌بندی‌ها را کشف کن</span></div><div className="categories">{categories.map((item) => <button key={item.label} type="button" onClick={() => setCategory(item.label)} className={"category " + (category === item.label ? "active" : "")} aria-pressed={category === item.label}><span className="category-icon">{item.icon}</span><strong>{item.label}</strong><span className="category-arrow">↗</span></button>)}</div></section>

      <section className="shell section" id="products"><div className="section-head"><div><span className="eyebrow">CURATED FOR YOU</span><h2>منتخب‌های این هفته<span className="dot">.</span></h2></div><button type="button" className="clear-btn" onClick={() => {setCategory("همه محصولات");setQuery("");}}>نمایش همه محصولات ←</button></div>
        {visible.length ? <div className="products">{visible.map((p) => <article className="product" key={p.id}><div className={"product-photo " + p.tone}><span className="product-badge">{p.badge || "پیشنهادی"}</span><button className={"heart " + (liked.includes(p.id) ? "selected" : "")} type="button" aria-label={"افزودن یا حذف " + p.name + " از علاقه‌مندی"} aria-pressed={liked.includes(p.id)} onClick={() => toggleLiked(p.id)}>{liked.includes(p.id) ? "♥" : "♡"}</button><span className="product-art" role="img" aria-label={p.name}>{p.art}</span></div><div className="product-meta"><small>{p.category} / NAVID SELECT</small><h3>{p.name}</h3><div className="price"><strong>{p.price} <small>تومان</small></strong>{p.oldPrice && <del>{p.oldPrice}</del>}</div><button className="buy" type="button" onClick={() => setCartCount((n) => n + 1)}>افزودن به سبد آزمایشی <span>＋</span></button></div></article>)}</div> : <div className="empty">محصولی با این جست‌وجو پیدا نشد. دسته دیگری انتخاب کن.</div>}
      </section>

      <section className="shell ending"><span>MORE THAN A STORE</span><h2>هر روز، یک انتخاب بهتر.</h2><p>طراحی اولیه رابط کاربری با قابلیت توسعه در v0 و تبدیل مرحله‌ای به قالب واقعی ووکامرس.</p></section>
      <footer className="shell footer"><strong>نوید<span className="brand-accent">شاپ</span></strong><small>این صفحه فقط پیش‌نمایش بصری است؛ قیمت‌ها و سبد خرید نمونه هستند.</small><span>© ۲۰۲۶</span></footer>
    </main>
  );
}

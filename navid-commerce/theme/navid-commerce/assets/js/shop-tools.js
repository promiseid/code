/* Public WooCommerce Store API + bounded browser-local shopping lists. No credentials. */
(() => {
 'use strict';
 const config=window.navidStore;
 const dialog=document.querySelector('#nc-shopping-tools');
 if(!config||!dialog||!dialog.showModal) return;
 document.documentElement.classList.add('nc-tools-ready');
 const state={wishlist:[],compare:[],recent:[]};
 const products=new Map();
 const validIds=v=>Array.isArray(v)?[...new Set(v.filter(x=>Number.isSafeInteger(x)&&x>0))].slice(0,20):[];
 for(const key of Object.keys(state)) {try {state[key]=validIds(JSON.parse(localStorage.getItem('navid-'+key)||'[]')).slice(0,key==='compare'?4:key==='recent'?8:20);}catch{}}
 const persist=key=>{try{localStorage.setItem('navid-'+key,JSON.stringify(state[key]));}catch{}};
 const safeURL=(value,link=false)=>{try{const u=new URL(value,location.href);return /^https?:$/.test(u.protocol)&&(!link||u.origin===location.origin)?u.href:'';}catch{return '';}};
 const el=(tag,className,text)=>{const n=document.createElement(tag);if(className)n.className=className;if(text!==undefined)n.textContent=String(text);return n;};
 const notice=document.querySelector('.nc-toast');let toastTimer;
 const toast=text=>{notice.textContent=text;notice.classList.add('is-visible');clearTimeout(toastTimer);toastTimer=setTimeout(()=>notice.classList.remove('is-visible'),3600);};
 const refresh=()=>{
  document.querySelectorAll('[data-tool-count]').forEach(n=>{const count=state[n.dataset.toolCount]?.length||0;n.textContent=count;const b=n.closest('button');if(b){b.dataset.toolLabel ||= b.getAttribute('aria-label');b.setAttribute('aria-label',b.dataset.toolLabel+'؛ '+count+' محصول');}});
  document.querySelectorAll('[data-nc-product]').forEach(box=>{
   try {const p=JSON.parse(box.dataset.ncProduct);if(!Number.isSafeInteger(p.id)||p.id<1)return;products.set(p.id,p);box.querySelectorAll('[data-save-tool]').forEach(button=>button.setAttribute('aria-pressed',String(state[button.dataset.saveTool].includes(p.id))));}catch{}
  });
 };
 refresh();
 const current=document.querySelector('.summary [data-nc-product]');
 if(current) {try{const p=JSON.parse(current.dataset.ncProduct);state.recent=[p.id,...state.recent.filter(id=>id!==p.id)].slice(0,8);persist('recent');}catch{}}
 const heading=dialog.querySelector('#nc-tools-title'),content=dialog.querySelector('.nc-tools-content'),status=dialog.querySelector('.nc-tools-status');
 let active='wishlist',returnFocus,requestController;
 const titles={wishlist:'علاقه‌مندی‌های شما',compare:'مقایسه انتخاب‌ها',recent:'آخرین بازدیدهای شما'};
 const image=p=>{const img=el('img');img.width=240;img.height=200;img.alt=p.name||'';img.loading='lazy';const src=safeURL(p.image)||safeURL(config.placeholder);if(src)img.src=src;return img;};
 const productLink=(p,label)=>{const a=el('a','nc-tool-product-link',label||p.name);const href=safeURL(p.url,true);if(href)a.href=href;return a;};
 const removeButton=(p,key)=>{const b=el('button','nc-remove-item','حذف');b.type='button';b.dataset.removeTool=key;b.dataset.productId=p.id;b.setAttribute('aria-label','حذف '+p.name+' از '+titles[key]);return b;};
 const render=()=>{
  content.replaceChildren();heading.textContent=titles[active];
  const list=state[active].map(id=>products.get(id)).filter(Boolean);
  if(!state[active].length){content.append(el('p','nc-list-empty',active==='compare'?'برای مقایسه، دکمه دو پیکان روی محصولات را بزنید.':'هنوز محصولی در این فهرست نیست.'));return;}
  if(!list.length){content.append(el('p','nc-list-empty','اطلاعات این محصولات در دسترس نیست.'));return;}
  if(active==='compare') {
   const wrap=el('div','nc-compare-scroll');wrap.tabIndex=0;wrap.setAttribute('role','region');wrap.setAttribute('aria-label','جدول مقایسه محصولات؛ برای مشاهده ستون‌ها پیمایش کنید');
   const table=el('table','nc-compare-table');const caption=el('caption','screen-reader-text','مقایسه قیمت، موجودی و ویژگی‌های محصولات');table.append(caption);
   const head=el('thead'),tr=el('tr');tr.append(el('th','','محصول'));
   list.forEach(p=>{const th=el('th');th.scope='col';th.append(image(p),productLink(p),removeButton(p,'compare'));tr.append(th);});head.append(tr);table.append(head);
   const rows=[['قیمت',p=>p.price],['موجودی',p=>p.stock],['دسته‌بندی',p=>p.category]];
   const attrs=[...new Set(list.flatMap(p=>Object.keys(p.attributes||{})))].slice(0,16);
   attrs.forEach(key=>rows.push([key,p=>(p.attributes||{})[key]||'—']));
   rows.push(['جزئیات',p=>p.description||'—']);const body=el('tbody');
   rows.forEach(([label,read])=>{const row=el('tr');const th=el('th','',label);th.scope='row';row.append(th);list.forEach(p=>row.append(el('td','',read(p)||'—')));body.append(row);});table.append(body);wrap.append(table);content.append(wrap);
  } else {
   const grid=el('div','nc-saved-grid');list.forEach(p=>{const card=el('article','nc-saved-card');card.append(image(p),productLink(p),el('span','nc-saved-stock',p.stock),el('strong','',p.price),productLink(p,'مشاهده و خرید'),removeButton(p,active));grid.append(card);});content.append(grid);
  }
 };
 const normalize=p=>{
  const minor=Math.min(6,Math.max(0,Number(p.prices?.currency_minor_unit)||0));
  const amount=Number(p.prices?.price)/10**minor;
  const price=Number.isFinite(amount)?new Intl.NumberFormat(document.documentElement.lang||'fa-IR',{maximumFractionDigits:minor}).format(amount)+' '+(p.prices.currency_symbol||p.prices.currency_code):'قیمت در صفحه محصول';
  const attributes={};(p.attributes||[]).forEach(a=>{if(a.name&&a.terms)attributes[a.name]=a.terms.map(t=>t.name).join('، ');});
  return {id:p.id,name:p.name,url:p.permalink,image:p.images?.[0]?.thumbnail||p.images?.[0]?.src,price,stock:p.is_in_stock?'موجود و قابل سفارش':'در حال حاضر ناموجود',category:(p.categories||[]).map(c=>c.name).join('، '),attributes,description:''};
 };
 const fetchProducts=async(params,signal)=>{
  const url=new URL(config.api,location.href);Object.entries(params).forEach(([k,v])=>url.searchParams.set(k,String(v)));
  const response=await fetch(url,{signal,credentials:'same-origin',headers:{Accept:'application/json'}});if(!response.ok)throw new Error('Product request failed');const data=await response.json();if(!Array.isArray(data))throw new Error('Invalid product response');return data;
 };
 const open=async(key,button)=>{
  if(!Object.hasOwn(state,key))return;active=key;returnFocus=button;requestController?.abort();requestController=new AbortController();
  dialog.classList.toggle('is-compare',key==='compare');render();status.textContent=state[key].length?'در حال تازه‌سازی قیمت و موجودی…':'';dialog.showModal();document.documentElement.classList.add('nc-dialog-open');dialog.querySelector('.nc-tools-close').focus();
  if(!state[key].length)return;
  const selected=key;const requested=[...state[key]];const controller=requestController;const timeout=setTimeout(()=>controller.abort(),12000);
  try {
   const rows=await fetchProducts({include:requested.join(','),per_page:20,catalog_visibility:'visible',_fields:'id,name,permalink,images,prices,is_in_stock,categories,attributes'},controller.signal);
   if(!dialog.open||selected!==active||controller.signal.aborted)return;
   const available=new Set();rows.forEach(p=>{const fresh=normalize(p);const prev=products.get(p.id);products.set(p.id,{...prev,...fresh,description:prev?.description||''});available.add(p.id);});
   requested.filter(id=>!available.has(id)).forEach(id=>products.delete(id));render();status.textContent=rows.length===requested.length?'قیمت و موجودی به‌روز شد.':'برخی محصولات دیگر در فهرست عمومی فروشگاه در دسترس نیستند.';
  }catch {if(dialog.open&&selected===active&&controller===requestController){status.textContent='دریافت اطلاعات تازه ممکن نشد. قیمت نهایی را در صفحه محصول بررسی کنید.';render();}}
  finally{clearTimeout(timeout);}
 };
 document.addEventListener('click',event=>{
  const save=event.target.closest('[data-save-tool]');
  if(save){const key=save.dataset.saveTool;if(!Object.hasOwn(state,key))return;let p;try{p=JSON.parse(save.closest('[data-nc-product]').dataset.ncProduct);}catch{return;}if(!Number.isSafeInteger(p.id)||p.id<1)return;
   const exists=state[key].includes(p.id);if(!exists&&state[key].length>=(key==='compare'?4:20)){toast(key==='compare'?'حداکثر ۴ محصول را می‌توانید مقایسه کنید.':'فهرست علاقه‌مندی به سقف ۲۰ محصول رسیده است.');return;}
   state[key]=exists?state[key].filter(id=>id!==p.id):[...state[key],p.id];persist(key);refresh();toast(exists?'محصول از فهرست حذف شد.':key==='compare'?'محصول به مقایسه اضافه شد.':'محصول به علاقه‌مندی‌ها اضافه شد.');return;
  }
  const opener=event.target.closest('[data-open-tool]');if(opener){open(opener.dataset.openTool,opener);return;}
  const remove=event.target.closest('[data-remove-tool]');if(remove){const key=remove.dataset.removeTool;state[key]=state[key].filter(id=>id!==Number(remove.dataset.productId));persist(key);refresh();render();dialog.querySelector('.nc-tools-close').focus();}
 });
 dialog.querySelector('.nc-tools-close').addEventListener('click',()=>dialog.close());
 dialog.addEventListener('click',e=>{if(e.target===dialog)dialog.close();});
 dialog.addEventListener('close',()=>{requestController?.abort();document.documentElement.classList.remove('nc-dialog-open');returnFocus?.focus();});
 window.addEventListener('storage',event=>{const key=event.key?.replace('navid-','');if(Object.hasOwn(state,key)){try{state[key]=validIds(JSON.parse(event.newValue||'[]')).slice(0,key==='compare'?4:20);refresh();if(dialog.open)render();}catch{}}});
 // Native search submission stays available. Suggestions are an enhancement with Escape/Tab support.
 document.querySelectorAll('.header-search .search-form').forEach(form=>{
  const input=form.querySelector('input[type=search]');if(!input)return;const panel=el('div','nc-search-results');panel.id=input.id+'-suggestions';panel.hidden=true;panel.setAttribute('aria-label','پیشنهادهای جستجو');const feedback=el('p','nc-search-feedback');feedback.setAttribute('role','status');panel.append(feedback);form.parentElement.append(panel);input.setAttribute('aria-controls',panel.id);input.autocomplete='off';let timer,controller;
  const hide=()=>{panel.hidden=true;};
  input.addEventListener('input',()=>{clearTimeout(timer);controller?.abort();const query=input.value.trim();if(query.length<2){hide();return;}
   timer=setTimeout(async()=>{controller=new AbortController();const current=controller;panel.replaceChildren(feedback);feedback.textContent='در حال جستجو…';panel.hidden=false;const timeout=setTimeout(()=>current.abort(),10000);
    try{const rows=await fetchProducts({search:query,per_page:6,catalog_visibility:'search',_fields:'id,name,permalink,images,prices,is_in_stock'},current.signal);if(current.signal.aborted||query!==input.value.trim())return;
     feedback.textContent=rows.length?rows.length+' محصول پیدا شد':'محصولی پیدا نشد. جستجوی دیگری را امتحان کنید.';
     rows.forEach(raw=>{const p=normalize(raw),a=productLink(p);a.className='nc-search-result';a.replaceChildren(image(p),el('span','',p.name),el('strong','',p.price));panel.append(a);});
    }catch{if(!current.signal.aborted){feedback.textContent='پیشنهادها در دسترس نیستند؛ برای جستجوی کامل Enter بزنید.';}}finally{clearTimeout(timeout);}
   },280);
  });
  form.addEventListener('keydown',e=>{if(e.key==='Escape'){controller?.abort();hide();input.focus();}});
  form.addEventListener('submit',()=>{clearTimeout(timer);controller?.abort();hide();});
  panel.addEventListener('keydown',e=>{if(e.key==='Escape'){hide();input.focus();}});
  document.addEventListener('click',e=>{if(!form.parentElement.contains(e.target)){clearTimeout(timer);controller?.abort();hide();}});
 });
})();

/* Motion lifecycle: respect reduced motion, data saver, offscreen and background tabs. */
(() => {
 'use strict';
 const root=document.documentElement,stage=document.querySelector('.nc-product-stage');
 const media=matchMedia('(prefers-reduced-motion: reduce)');
 let override;try{const saved=localStorage.getItem('navid-motion');if(saved==='paused'||saved==='playing')override=saved;}catch{}
 let paused=override?override==='paused':media.matches;
 const apply=()=>{
  root.classList.toggle('nc-motion-paused',paused);
  document.querySelectorAll('.nc-motion-toggle').forEach(b=>{b.setAttribute('aria-pressed',String(paused));b.querySelector('span').textContent=paused?'شروع حرکت':'توقف حرکت';});
  document.dispatchEvent(new CustomEvent('navid:motion',{detail:{paused}}));
 };
 apply();
 document.querySelectorAll('.nc-motion-toggle').forEach(b=>b.addEventListener('click',()=>{paused=!paused;override=paused?'paused':'playing';try{localStorage.setItem('navid-motion',override);}catch{}apply();}));
 media.addEventListener('change',()=>{if(!override){paused=media.matches;apply();}});
 // Native purchase forms remain authoritative; the bar only scrolls to them.
 const sticky=document.querySelector('.nc-sticky-buy'),purchase=document.querySelector('.summary form.cart');
 if(sticky&&purchase&&'IntersectionObserver' in window){new IntersectionObserver(entries=>{const e=entries[0];sticky.classList.toggle('is-visible',!e.isIntersecting&&e.boundingClientRect.bottom<0);},{threshold:0}).observe(purchase);}
 if(!stage||stage.dataset.sceneEnabled!=='1') {if(stage){stage.querySelector('.nc-scene-enable').hidden=true;stage.querySelector('.nc-scene-enable').style.display='none';}return;}
 let started=false;
 const button=stage.querySelector('.nc-scene-enable'),label=button.querySelector('span'),status=stage.querySelector('.nc-scene-status');
 const start=async(explicit=false)=>{
  if(started||(!explicit&&(paused||navigator.connection?.saveData||matchMedia('(max-width:800px)').matches)))return;started=true;label.textContent='آماده‌سازی…';button.disabled=true;
  try {const scene=await import(stage.dataset.sceneModule);await scene.mountScene(stage);label.textContent='چرخش با کشیدن';button.disabled=false;button.addEventListener('click',()=>stage.querySelector('canvas').focus());}
  catch {started=false;button.disabled=false;label.textContent='نمای سه‌بعدی';status.textContent='نمای سه‌بعدی در این مرورگر آماده نشد؛ تصویر محصول نمایش داده می‌شود.';}
 };
 button.addEventListener('click',()=>{if(!started)start(true);});
 if('IntersectionObserver' in window){const io=new IntersectionObserver(entries=>{if(entries[0].isIntersecting&&!paused&&!navigator.connection?.saveData){io.disconnect();const schedule=()=>('requestIdleCallback'in window?requestIdleCallback(()=>start(),{timeout:1800}):setTimeout(()=>start(),300));if(document.readyState==='complete')schedule();else window.addEventListener('load',schedule,{once:true});}},{rootMargin:'50px'});io.observe(stage);}
 document.addEventListener('navid:motion',e=>{if(!e.detail.paused&&!started)start();});
})();

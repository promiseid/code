import fs from 'node:fs';import path from 'node:path';import {createRequire} from 'node:module';
const require=createRequire(import.meta.url);export const {chromium}=require(process.env.NAVID_PLAYWRIGHT_PATH||'playwright');export const AxeBuilder=require(process.env.NAVID_AXE_PATH||'@axe-core/playwright').default;
export const base=process.env.NAVID_TEST_URL||'http://127.0.0.1:9400',out=path.resolve(process.env.NAVID_TEST_RESULTS||'test-results')+'/';fs.mkdirSync(out,{recursive:true});
export const launch=()=>chromium.launch({headless:true,...(process.env.NAVID_CHROME_PATH?{executablePath:process.env.NAVID_CHROME_PATH}:{}),args:['--no-sandbox','--enable-unsafe-swiftshader','--disable-dev-shm-usage']});
export const go=(p,url)=>p.goto(new URL(url,base).href,{waitUntil:'networkidle',timeout:90000});
export async function signin(p,url='/wp-login.php',name=process.env.NAVID_TEST_USER||'admin',password=process.env.NAVID_TEST_PASSWORD||'NavidDemo!2026'){await go(p,url);await p.locator('#user_login').fill(name);await p.locator('#user_pass').fill(password);await p.locator('#wp-submit').click();await p.waitForURL(/wp-admin\//,{timeout:90000});await p.waitForLoadState('networkidle');}
export async function tab(p,name){await go(p,'/wp-admin/admin.php?page=navid-commerce&tab='+name);}
export async function save(p){await Promise.all([p.waitForNavigation({waitUntil:'networkidle',timeout:90000}),p.locator('.nc-settings-form #submit').click()]);}
export const axe=async p=>{const a=await new AxeBuilder({page:p}).withTags(['wcag2a','wcag2aa','wcag21a','wcag21aa']).analyze();return a.violations.map(v=>({id:v.id,nodes:v.nodes.map(n=>({target:n.target,summary:n.failureSummary}))}));};

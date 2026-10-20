import fs from 'node:fs';import path from 'node:path';import {createRequire} from 'node:module';
const require=createRequire(import.meta.url);const esbuild=process.env.NAVID_ESBUILD_PATH?require(process.env.NAVID_ESBUILD_PATH):await import('esbuild');
const root=path.resolve(new URL('../theme/navid-commerce',import.meta.url).pathname),dist=root+'/assets/dist';fs.mkdirSync(dist,{recursive:true});
for(const layout of ['market','studio'])for(const page of ['home','store']){
 const sources=['fonts','main',page==='home'?'shop-home':'shop','studio',...(layout==='market'?['market']:[])];
 const input=sources.map(n=>fs.readFileSync(root+'/assets/css/'+n+'.css','utf8')).join('\n');const result=await esbuild.transform(input,{loader:'css',minify:true,target:'es2022'});fs.writeFileSync(dist+'/frontend-'+page+'-'+layout+'.min.css',result.code);
}
const js=['main','motion','shop-tools','market'].map(n=>fs.readFileSync(root+'/assets/js/'+n+'.js','utf8')).join('\n;\n');const result=await esbuild.transform(js,{loader:'js',minify:true,target:'es2022'});fs.writeFileSync(dist+'/frontend.min.js',result.code);console.log('Built four route stylesheets and one shared theme JS entry.');

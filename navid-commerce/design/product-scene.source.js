/* Original procedural studio. Three.js 0.186.1, bundled locally; no external assets. */
import * as THREE from 'three';
export async function mountScene(stage) {
 const canvas=stage.querySelector('canvas'),surface=stage.querySelector('.nc-scene-surface');
 const renderer=new THREE.WebGLRenderer({canvas,alpha:true,antialias:true,powerPreference:'low-power'});
 renderer.setPixelRatio(Math.min(devicePixelRatio,1.35));renderer.outputColorSpace=THREE.SRGBColorSpace;renderer.toneMapping=THREE.ACESFilmicToneMapping;renderer.toneMappingExposure=1.35;
 const scene=new THREE.Scene(),camera=new THREE.PerspectiveCamera(36,1,.1,40);camera.position.set(4.8,2.5,7.8);camera.lookAt(0,.15,0);
 scene.add(new THREE.HemisphereLight(0xe2f0ff,0x172536,3));
 [[0xffffff,85,[4,6,4]],[0xa5ffcf,45,[-4,1,2]],[0xb4a0ff,75,[1,3,-4]]].forEach(([color,intensity,pos])=>{const l=new THREE.PointLight(color,intensity,20,2);l.position.set(...pos);scene.add(l);});
 const env=document.createElement('canvas');env.width=256;env.height=128;const ctx=env.getContext('2d'),g=ctx.createLinearGradient(0,0,256,128);
 [[0,'#f1f8ff'],[.22,'#899aa5'],[.45,'#172333'],[.65,'#91b8a6'],[1,'#0b0f1a']].forEach(([p,c])=>g.addColorStop(p,c));ctx.fillStyle=g;ctx.fillRect(0,0,256,128);ctx.fillStyle='#f0f8ff';ctx.fillRect(25,15,30,75);ctx.fillStyle='#c5f9e0';ctx.fillRect(170,20,14,90);
 const environment=new THREE.CanvasTexture(env);environment.mapping=THREE.EquirectangularReflectionMapping;scene.environment=environment;
 const shell=new THREE.MeshStandardMaterial({color:0x30353f,metalness:.77,roughness:.29,envMapIntensity:1.1}),metal=new THREE.MeshStandardMaterial({color:0xa8b9bf,metalness:.88,roughness:.3}),padding=new THREE.MeshStandardMaterial({color:0x121722,metalness:.04,roughness:.85}),inner=new THREE.MeshStandardMaterial({color:0x070b12,roughness:1}),accent=new THREE.MeshStandardMaterial({color:0xb7ffc7,metalness:.25,roughness:.32,emissive:0x4cbb87,emissiveIntensity:.35});
 const root=new THREE.Group();scene.add(root);
 const mesh=(geometry,material,parent=root)=>{const m=new THREE.Mesh(geometry,material);parent.add(m);return m;};
 const arch=(outer,inside,depth)=>{const s=new THREE.Shape();s.moveTo(-outer,0);s.absarc(0,0,outer,Math.PI,0,true);s.lineTo(inside,0);s.absarc(0,0,inside,0,Math.PI,false);s.closePath();const shape=new THREE.ExtrudeGeometry(s,{depth,bevelEnabled:true,bevelSegments:3,steps:1,bevelSize:.035,bevelThickness:.025,curveSegments:48});shape.translate(0,0,-depth/2);return shape;};
 mesh(arch(1.6,1.4,.34),shell).position.y=.3;mesh(arch(1.4,1.31,.29),padding).position.y=.3;
 for(const side of [-1,1]){
  mesh(new THREE.BoxGeometry(.12,.8,.24),metal).position.set(side*1.46,.04,0);mesh(new THREE.CapsuleGeometry(.095,.38,4,12),shell).position.set(side*1.44,-.17,.06);
  const ear=new THREE.Group();ear.position.set(side*1.39,-.54,.05);ear.rotation.z=side*.12;root.add(ear);
  const cylinder=(radius,depth,material,x)=>{const m=mesh(new THREE.CylinderGeometry(radius,radius,depth,48),material,ear);m.rotation.z=Math.PI/2;m.scale.z=1.15;m.position.x=x;return m;};
  cylinder(.77,.36,shell,side*.06);cylinder(.70,.075,metal,side*.27);cylinder(.67,.055,shell,side*.32);
  const trim=mesh(new THREE.TorusGeometry(.64,.013,8,64),accent,ear);trim.rotation.y=Math.PI/2;trim.scale.y=1.14;trim.position.x=side*.36;
  const cushion=mesh(new THREE.TorusGeometry(.56,.18,16,48),padding,ear);cushion.rotation.y=Math.PI/2;cushion.scale.y=1.18;cushion.position.x=-side*.27;cylinder(.49,.035,inner,-side*.3);
  for(let i=0;i<5;i++){const h=[.12,.24,.33,.24,.12][i],bar=mesh(new THREE.CapsuleGeometry(.015,h,3,6),metal,ear);bar.position.set(side*.36,h*.25,(i-2)*.08);}
  mesh(new THREE.SphereGeometry(.022,10,8),accent,ear).position.set(side*.36,-.48,.02);
  for(let i=0;i<3;i++)mesh(new THREE.BoxGeometry(.025,.018,.065),inner,ear).position.set(side*.30,-.60,(i-1)*.1);
 }
 root.rotation.set(.05,.15,-.24);root.position.y=.1;
 let targetY=.15,targetX=.05,dragging=false,lastX=0,lastY=0,visible=true,paused=document.documentElement.classList.contains('nc-motion-paused'),destroyed=false,lastFrame=0,time=0;
 const render=()=>{if(!destroyed)renderer.render(scene,camera);};
 const size=()=>{const r=surface.getBoundingClientRect();renderer.setSize(r.width,r.height,false);camera.aspect=r.width/r.height;camera.fov=r.width<400?40:36;camera.updateProjectionMatrix();render();};
 const animate=now=>{if(destroyed||!visible||document.hidden||paused||now-lastFrame<32)return;time+=Math.min((now-lastFrame)/1000,.05);lastFrame=now;root.rotation.y+=(targetY-root.rotation.y)*.07;root.rotation.x+=(targetX-root.rotation.x)*.07;root.position.y=.1+Math.sin(time*.8)*.08;root.rotation.z=-.24+Math.sin(time*.45)*.025;render();};
 const sync=()=>{renderer.setAnimationLoop(!destroyed&&visible&&!document.hidden&&!paused?animate:null);render();};
 const onMotion=e=>{paused=e.detail.paused;sync();},onVisibility=()=>sync();
 canvas.addEventListener('pointerdown',e=>{dragging=true;lastX=e.clientX;lastY=e.clientY;canvas.setPointerCapture(e.pointerId);});
 canvas.addEventListener('pointermove',e=>{if(!dragging)return;targetY+=(e.clientX-lastX)*.012;targetX=THREE.MathUtils.clamp(targetX+(e.clientY-lastY)*.005,-.5,.6);lastX=e.clientX;lastY=e.clientY;if(paused){root.rotation.y=targetY;root.rotation.x=targetX;render();}});
 canvas.addEventListener('pointerup',()=>{dragging=false;});canvas.addEventListener('pointercancel',()=>{dragging=false;});
 canvas.addEventListener('keydown',e=>{if(!['ArrowLeft','ArrowRight','ArrowUp','ArrowDown'].includes(e.key))return;e.preventDefault();if(e.key==='ArrowLeft')targetY-=.2;if(e.key==='ArrowRight')targetY+=.2;if(e.key==='ArrowUp')targetX=Math.max(-.5,targetX-.1);if(e.key==='ArrowDown')targetX=Math.min(.6,targetX+.1);if(paused){root.rotation.y=targetY;root.rotation.x=targetX;render();}});
 const colorListeners=[];stage.querySelectorAll('[data-scene-color]').forEach(b=>{const fn=()=>{shell.color.set(b.dataset.sceneColor);stage.querySelectorAll('[data-scene-color]').forEach(x=>x.setAttribute('aria-pressed',String(x===b)));render();};b.addEventListener('click',fn);colorListeners.push([b,fn]);});
 const resize=new ResizeObserver(size);resize.observe(surface);const observer=new IntersectionObserver(e=>{visible=e[0].isIntersecting;sync();},{threshold:.03});observer.observe(stage);
 document.addEventListener('visibilitychange',onVisibility);document.addEventListener('navid:motion',onMotion);
 canvas.addEventListener('webglcontextlost',e=>{e.preventDefault();renderer.setAnimationLoop(null);stage.classList.remove('is-scene-ready');canvas.hidden=true;stage.querySelector('.nc-scene-status').textContent='نمای سه‌بعدی متوقف شد؛ تصویر جایگزین در دسترس است.';});
 size();await renderer.compileAsync(scene,camera);canvas.hidden=false;render();stage.classList.add('is-scene-ready');stage.dataset.sceneEngine='three-webgl';sync();
 const cleanup=()=>{if(destroyed)return;destroyed=true;renderer.setAnimationLoop(null);resize.disconnect();observer.disconnect();document.removeEventListener('visibilitychange',onVisibility);document.removeEventListener('navid:motion',onMotion);colorListeners.forEach(([b,fn])=>b.removeEventListener('click',fn));root.traverse(o=>{if(o.geometry)o.geometry.dispose();});[shell,metal,padding,inner,accent].forEach(m=>m.dispose());environment.dispose();renderer.dispose();};
 window.addEventListener('pagehide',e=>{if(e.persisted){visible=false;sync();}else cleanup();});window.addEventListener('pageshow',e=>{if(e.persisted){visible=true;sync();}});return {dispose:cleanup};
}

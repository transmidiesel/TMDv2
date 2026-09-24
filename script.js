// =========================================================
// TRANSMIDIESEL — Interacciones
// =========================================================

// ---- Navbar scroll transform (se encoge cuando el hero termina) ----
const navHeader = document.getElementById('navHeader');

function updateNavbarState(){
  if (!navHeader) return;

  const heroScrollEl = document.getElementById('inicio');
  const scrollTop = window.scrollY || document.documentElement.scrollTop;

  let trigger = 40; // fallback

  if (heroScrollEl) {
    // Calculamos la posición real del final del hero en el documento
    const rect = heroScrollEl.getBoundingClientRect();
    const heroTop = rect.top + scrollTop;              // dónde empieza el hero en el documento
    const heroHeight = heroScrollEl.offsetHeight;      // alto total del hero (300vh)
    const heroEnd = heroTop + heroHeight - window.innerHeight; // momento en que el sticky se suelta

    trigger = Math.max(heroEnd - 40, 40);              // 40px de margen
  }

  navHeader.classList.toggle('scrolled', scrollTop > trigger);
}

window.addEventListener('scroll', updateNavbarState, {passive:true});
window.addEventListener('resize', updateNavbarState);
window.addEventListener('load', updateNavbarState);
updateNavbarState();

// ---- Cursor glow (desktop only) ----
const glow = document.getElementById('cursorGlow');
if (window.matchMedia('(hover:hover) and (pointer:fine)').matches) {
  window.addEventListener('mousemove', e=>{
    glow.style.left = e.clientX+'px';
    glow.style.top = e.clientY+'px';
    glow.classList.add('active');
  });
  document.addEventListener('mouseleave', ()=>glow.classList.remove('active'));
}

// ---- Scroll reveal (bidireccional) + contadores animados, unificado en un solo observer ----
const revealEls = document.querySelectorAll('.reveal');
const runningAnims = new WeakMap();

function animateCounter(el){
  const target = parseInt(el.dataset.target, 10);
  if (isNaN(target)) return;

  const prevId = runningAnims.get(el);
  if (prevId) cancelAnimationFrame(prevId);

  const duration = 1600;
  const startTime = performance.now();
  let lastText = '';

  const step = (now) => {
    const elapsed = now - startTime;
    const progress = Math.min(elapsed / duration, 1);
    const eased = 1 - Math.pow(1 - progress, 3);
    const value = Math.floor(eased * target);
    const text = value.toLocaleString('es-CO');
    if (text !== lastText) { el.textContent = text; lastText = text; }

    if (progress < 1) {
      const id = requestAnimationFrame(step);
      runningAnims.set(el, id);
    } else {
      el.textContent = target.toLocaleString('es-CO');
      runningAnims.delete(el);
    }
  };

  const id = requestAnimationFrame(step);
  runningAnims.set(el, id);
}

function resetCounter(el){
  const id = runningAnims.get(el);
  if (id) cancelAnimationFrame(id);
  runningAnims.delete(el);
  el.textContent = '0';
}

const io = new IntersectionObserver((entries)=>{
  entries.forEach(en=>{
    en.target.classList.toggle('visible', en.isIntersecting);

    // Si esta tarjeta .reveal contiene un contador, lo animamos/reiniciamos
    // con la MISMA lectura de intersección — sin un segundo observer compitiendo
    // por calcular la geometría de un elemento que se está transformando.
    const counter = en.target.querySelector('.count');
    if (counter) {
      if (en.isIntersecting) animateCounter(counter);
      else resetCounter(counter);
    }
  });
}, {threshold:0.15, rootMargin:'0px 0px -6% 0px'});
revealEls.forEach(el=>io.observe(el));

// ---- Magnetic buttons ----
document.querySelectorAll('.magnetic').forEach(btn=>{
  btn.addEventListener('mousemove', e=>{
    const r = btn.getBoundingClientRect();
    const x = e.clientX - r.left - r.width/2;
    const y = e.clientY - r.top - r.height/2;
    btn.style.transform = `translate(${x*0.18}px, ${y*0.28}px)`;
  });
  btn.addEventListener('mouseleave', ()=>{ btn.style.transform=''; });
});

// ---- Mobile burger (simple toggle of links) ----
const burger = document.getElementById('burgerBtn');
const links = document.querySelector('nav.links');
burger.addEventListener('click', ()=>{
  const isOpen = links.style.display === 'flex';
  links.style.cssText = isOpen ? '' : 'display:flex;flex-direction:column;position:fixed;top:74px;right:20px;left:20px;background:rgba(26,26,26,0.96);backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,0.1);border-radius:16px;padding:14px;gap:4px;';
});

// ---- WhatsApp module ----
const waFab = document.getElementById('waFab');
const waPanel = document.getElementById('waPanel');
waFab.addEventListener('click', ()=> waPanel.classList.toggle('open'));

// ---- Carrusel de sectores (solo existe en quienes-somos.html) ----
const sectorCarousel = document.getElementById('sectorCarousel');
if (sectorCarousel) {
  const slides = sectorCarousel.querySelectorAll('.sector-slide');
  const dots = sectorCarousel.querySelectorAll('.sector-dot');
  const nameEl = document.getElementById('sectorName');
  let current = 0;

  function showSector(index){
    slides.forEach((s, i) => s.classList.toggle('active', i === index));
    dots.forEach((d, i) => d.classList.toggle('active', i === index));
    if (nameEl && slides[index]) nameEl.textContent = slides[index].dataset.sector;
  }

  setInterval(() => {
    current = (current + 1) % slides.length;
    showSector(current);
  }, 4500);
}

// (El contador animado de indicadores ahora vive dentro del observer de .reveal, más arriba)

// ---- Pop-up escalonado para marcas y certificaciones (bidireccional) ----
const revealPops = document.querySelectorAll('.reveal-pop');
if (revealPops.length) {
  const popObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      entry.target.classList.toggle('visible', entry.isIntersecting);
    });
  }, { threshold: 0.15, rootMargin:'0px 0px -6% 0px' });

  revealPops.forEach(el => popObserver.observe(el));
}

// ---- Hero: video de fondo + capas de texto controladas por scroll ----
(function(){
  const heroScroll = document.getElementById('inicio');
  const heroVideo   = document.getElementById('heroVideo');
  const scrollHint  = document.getElementById('heroScrollHint');
  const layerA      = document.querySelector('.hero-layer-a');
  const layerB      = document.querySelector('.hero-layer-b');
  if (!heroScroll || !heroVideo) return;

  let duration = 0;
  let ticking  = false;
  let currentVideoTime = 0;
  let targetVideoTime  = 0;
  let scrubLoopRunning = false;

  const onMeta = () => { duration = heroVideo.duration || 0; update(); };
  heroVideo.addEventListener('loadedmetadata', onMeta);
  if (heroVideo.readyState >= 1) onMeta();
  heroVideo.pause();

  function scrubLoop(){
    if (duration) {
      currentVideoTime += (targetVideoTime - currentVideoTime) * 0.15;
      if (Math.abs(targetVideoTime - currentVideoTime) < 0.01) {
        currentVideoTime = targetVideoTime;
      }
      if (!heroVideo.seeking && Math.abs(heroVideo.currentTime - currentVideoTime) > 0.02) {
        try { heroVideo.currentTime = currentVideoTime; } catch(e){}
      }
    }

    // Si ya alcanzamos el objetivo, detenemos el loop: no tiene sentido seguir
    // gastando un frame cada 16ms en el resto de la página (indicadores, etc.)
    if (Math.abs(targetVideoTime - currentVideoTime) < 0.01) {
      scrubLoopRunning = false;
      return;
    }
    requestAnimationFrame(scrubLoop);
  }

  function ensureScrubLoop(){
    if (!scrubLoopRunning) {
      scrubLoopRunning = true;
      requestAnimationFrame(scrubLoop);
    }
  }

  function rangeProgress(p, start, end){
    return Math.min(Math.max((p - start) / (end - start), 0), 1);
  }

  function update(){
    ticking = false;

    const scrollTop = window.scrollY || document.documentElement.scrollTop;
    const start = heroScroll.offsetTop;
    const total = heroScroll.offsetHeight - window.innerHeight;
    if (total <= 0) return;

    let progress = (scrollTop - start) / total;
    progress = Math.min(Math.max(progress, 0), 1);

    if (duration) {
      targetVideoTime = progress * duration;
      ensureScrubLoop();
    }

    // Capa A: visible al inicio, se desvanece entre 0.05 y 0.30
    if (layerA) {
      const aOpacity = 1 - rangeProgress(progress, 0.05, 0.30);
      layerA.style.opacity = aOpacity;
      layerA.style.transform = `translateY(-50%) translateY(${(1 - aOpacity) * -30}px)`;
    }

    // Capa B: aparece entre 0.35 y 0.60, se queda hasta el final
    if (layerB) {
      const bOpacity = rangeProgress(progress, 0.35, 0.60);
      layerB.style.opacity = bOpacity;
      layerB.style.transform = `translateY(-50%) translateY(${(1 - bOpacity) * 30}px)`;
    }

    if (scrollHint) scrollHint.style.opacity = Math.max(1 - progress * 6, 0);
  }

  window.addEventListener('scroll', () => {
    if (!ticking) {
      ticking = true;
      window.requestAnimationFrame(update);
    }
  }, {passive:true});

  window.addEventListener('load', update);
  window.addEventListener('resize', update);
  update();
})();

// ---- Botón "Volver arriba" ----
(function(){
  const backTop = document.getElementById('backTop');
  const heroSection = document.getElementById('inicio');
  if (!backTop) return;

  function updateBackTop(){
    const heroHeight = heroSection ? heroSection.offsetHeight : 600;
    const trigger = heroHeight * 0.6;
    const scrollTop = window.scrollY || document.documentElement.scrollTop;

    if (scrollTop > trigger) {
      backTop.classList.add('visible');
    } else {
      backTop.classList.remove('visible');
    }
  }

  backTop.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });

  window.addEventListener('scroll', updateBackTop, { passive: true });
  window.addEventListener('resize', updateBackTop);
  updateBackTop();
})();
/* ================= PAGE TRANSITIONS ================= */
(function(){
  const pageContent    = document.getElementById('pageContent');
  const pageTransition = document.getElementById('pageTransition');
  if (!pageContent) return; // si alguna página no tiene el wrapper, no se toca su navegación

  const DURATION = 520; // debe coincidir con --pt-duration en styles.css
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const NAV_DELAY = reduceMotion ? 60 : DURATION - 40; // margen para que el fade alcance a verse

  // ---- Utilidades de ruta ----
  function normalizePath(pathname){
    // quita barra final (salvo raíz) para comparar "quienes-somos" === "quienes-somos/"
    if (pathname.length > 1 && pathname.endsWith('/')) pathname = pathname.slice(0, -1);
    return pathname || '/';
  }

  function segments(pathname){
    return pathname.split('/').filter(Boolean);
  }

  // ---- Historial interno en sessionStorage (best-effort) ----
  function readStack(){
    try{
      const raw = sessionStorage.getItem('tmdStack');
      const stack = raw ? JSON.parse(raw) : [];
      return Array.isArray(stack) ? stack : [];
    }catch(e){ return []; }
  }
  function writeStack(stack){
    try{ sessionStorage.setItem('tmdStack', JSON.stringify(stack)); }catch(e){}
  }

  const currentPath = normalizePath(window.location.pathname);
  let stack = readStack();
  if (!stack.length || stack[stack.length - 1] !== currentPath) {
    // Primera carga de la sesión, recarga manual, o llegada por URL directa/otro sitio
    stack.push(currentPath);
    writeStack(stack);
  }

  // ---- Determina la dirección hacia una ruta destino ----
  function computeDirection(targetPath){
    // 1) Si el destino es exactamente la página anterior en nuestra pila -> vamos "atrás"
    if (stack.length >= 2 && stack[stack.length - 2] === targetPath) {
      return 'back';
    }
    // 2) Heurística por jerarquía de carpetas: si el destino es un ancestro
    //    del path actual (ej. de /marcas/duramax/ a /marcas/ o a /), es "atrás"
    const curSeg = segments(currentPath);
    const tgtSeg = segments(targetPath);
    const isAncestor = tgtSeg.length < curSeg.length &&
      tgtSeg.every((seg, i) => seg === curSeg[i]);
    if (isAncestor) return 'back';

    // 3) Cualquier otro caso (hijo, mismo nivel, otra sección) -> "adelante"
    return 'forward';
  }

  // ---- Reproduce la animación de entrada ----
  // instant=false: el <head> ya dejó #pageContent oculto/desplazado antes del primer paint
  //                (llegó de un clic nuestro), solo hace falta soltar la clase.
  // instant=true : el contenido YA se pintó visible (típico del botón Atrás/Adelante nativo,
  //                que no podemos interceptar con preventDefault), así que forzamos el estado
  //                inicial nosotros mismos, sin transición, y luego lo animamos igual.
  function playEntrance(direction, instant){
    const incomingClass = direction === 'back' ? 'pt-incoming-left' : 'pt-incoming-right';

    if (instant) {
      pageContent.style.transition = 'none';
      document.documentElement.classList.add(incomingClass);
      void pageContent.offsetWidth; // fuerza reflow: el navegador "ve" el estado inicial
      pageContent.style.transition = '';
    }

    pageContent.style.willChange = 'transform, opacity, filter';
    requestAnimationFrame(() => {
      requestAnimationFrame(() => {
        document.documentElement.classList.remove('pt-incoming-left', 'pt-incoming-right');
        if (pageTransition) pageTransition.classList.remove('active');
        window.setTimeout(() => { pageContent.style.willChange = 'auto'; }, DURATION + 60);
      });
    });
  }

  // ---- Entrada: si esta carga viene de una navegación nuestra, animamos ----
  function consumePendingEntrance(){
    let dir = null;
    try{ dir = sessionStorage.getItem('tmdPendingDirection'); }catch(e){}

    if (dir) {
      try{ sessionStorage.removeItem('tmdPendingDirection'); }catch(e){}
      // El script inline del <head> ya dejó #pageContent en su posición de partida.
      playEntrance(dir, false);
      return;
    }

    // No hay dirección pendiente de un clic nuestro: puede ser primera visita,
    // recarga manual (F5), URL directa... o el botón "Atrás" del navegador, que
    // no podemos interceptar con preventDefault. Usamos la pila para adivinar:
    // si el path actual es exactamente el anterior en nuestra pila, lo tratamos
    // como "back" y reproducimos la animación igual (de forma retroactiva).
    if (stack.length >= 2 && stack[stack.length - 2] === currentPath) {
      stack.pop();
      writeStack(stack);
      playEntrance('back', true);
    }
    // Nota: el botón "Adelante" del navegador (rehacer un paso hacia una página
    // ya visitada) no siempre es distinguible de una navegación nueva sin la
    // View Transitions API nativa. No se rompe la navegación en ese caso — solo
    // puede no mostrar la animación direccional.
  }

  consumePendingEntrance();

  // Si por cualquier razón la página se restaura desde bfcache (botón atrás/adelante
  // del navegador sin recarga real), limpiamos cualquier clase residual al instante
  // (evita quedarse "atascada" en un estado de salida si el usuario volvió justo
  // cuando esta página estaba animando su salida).
  window.addEventListener('pageshow', (event) => {
    if (event.persisted) {
      document.documentElement.classList.remove('pt-incoming-left', 'pt-incoming-right');
      pageContent.classList.remove('pt-exit-left', 'pt-exit-right');
      pageContent.style.willChange = 'auto';
      if (pageTransition) pageTransition.classList.remove('active');
    }
  });

  // ---- Salida: interceptamos clics en enlaces internos ----
  document.addEventListener('click', (e) => {
    if (e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;

    const link = e.target.closest('a');
    if (!link) return;

   const href = link.getAttribute('href');
    if (!href) return;

    // Anclas dentro de la misma página: comportamiento normal, sin transición
    if (href.startsWith('#')) return;

    // No tocar target="_blank", descargas, mailto/tel/javascript
    if (link.target === '_blank') return;
    if (link.hasAttribute('download')) return;
    if (href.startsWith('mailto:') || href.startsWith('tel:') || href.startsWith('javascript:')) return;

    // Enlaces externos (otro dominio) siguen su curso normal
    let targetURL;
    try{ targetURL = new URL(link.href, window.location.origin); }
    catch(e){ return; }
    if (targetURL.origin !== window.location.origin) return;

    const targetPath = normalizePath(targetURL.pathname);

    // Si es la misma página (mismo path, sin ancla nueva), no animamos
    if (targetPath === currentPath && !href.includes('#')) return;

    e.preventDefault();

    const direction = computeDirection(targetPath);

    // Actualizamos nuestra pila ANTES de salir, para que la próxima página la lea correcta
    if (direction === 'back') {
      stack.pop();
    } else {
      stack.push(targetPath);
    }
    writeStack(stack);

    try{
      sessionStorage.setItem('tmdPendingDirection', direction);
    }catch(err){}

    pageContent.style.willChange = 'transform, opacity, filter';
    pageContent.classList.add(direction === 'back' ? 'pt-exit-right' : 'pt-exit-left');
    if (pageTransition) pageTransition.classList.add('active');

    const targetHref = targetURL.href + targetURL.hash; // preserva #ancla si el link la trae

    const navigate = () => { window.location.href = targetHref; };

    // Salvavidas: si algo impide la navegación normal, forzamos igual un poco después
    const safetyTimer = window.setTimeout(navigate, NAV_DELAY);
    window.addEventListener('pagehide', () => window.clearTimeout(safetyTimer), { once:true });
  });
})();
/* =========================================================
   FX LAYER — Agua interactiva, mecánica flotante, cursor
   personalizado y microinteracciones (capa agregada).
   IIFE aislado: no lee ni sobreescribe nada del código anterior.
   Nota: el reveal por scroll y los contadores animados YA existen
   arriba (bidireccionales, sobre .reveal y .count) — no se duplican
   aquí para no generar dos observers compitiendo por los mismos
   elementos.
   ========================================================= */
(function(){
  'use strict';

  var reduceMotion  = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var isFinePointer = window.matchMedia('(hover:hover) and (pointer:fine)').matches;

  /* ---------------------------------------------------------
     1) CANVAS DE AGUA INTERACTIVA
  --------------------------------------------------------- */
  var canvas = document.getElementById('water-canvas');
  if (canvas && canvas.getContext) {
    var ctx = canvas.getContext('2d');
    var W = 0, H = 0;
    var mouseX = -9999, mouseY = -9999;
    var hasPointer = false;

    function resizeCanvas(){
      W = canvas.width  = window.innerWidth;
      H = canvas.height = window.innerHeight;
    }
    resizeCanvas();
    window.addEventListener('resize', resizeCanvas);

    if (!reduceMotion) {
      var desktop        = window.matchMedia('(min-width:769px)').matches;
      var PARTICLE_COUNT = desktop ? 60 : 25;
      var SPARKLE_COUNT   = desktop ? 28 : 12;

      var caustics = [];
      for (var c = 0; c < 6; c++){
        caustics.push({
          x:Math.random()*W, y:Math.random()*H,
          baseR:180+Math.random()*80,
          speedX:(Math.random()-0.5)*0.15,
          speedY:(Math.random()-0.5)*0.15,
          phase:Math.random()*Math.PI*2
        });
      }

      var waveLines = [];
      for (var wl = 0; wl < 4; wl++){
        waveLines.push({ y:(H/5)*(wl+1), phase:Math.random()*Math.PI*2, speed:0.15+Math.random()*0.1 });
      }

      var particles = [];
      for (var p = 0; p < PARTICLE_COUNT; p++){
        particles.push({
          x:Math.random()*W, y:Math.random()*H,
          r:0.4+Math.random()*1.6,
          vx:(Math.random()-0.5)*0.12,
          vy:(Math.random()-0.5)*0.12,
          alpha:0.25+Math.random()*0.4,
          phase:Math.random()*Math.PI*2
        });
      }

      var sparkles = [];
      for (var s = 0; s < SPARKLE_COUNT; s++){
        sparkles.push({
          x:Math.random()*W, y:Math.random()*H,
          size:1+Math.random()*2.5,
          phase:Math.random()*Math.PI*2,
          driftX:(Math.random()-0.5)*0.05,
          driftY:(Math.random()-0.5)*0.05
        });
      }

      var ripples = [];
      var lastRippleTime = 0;
      var RIPPLE_THROTTLE = 220;
      var MAX_RIPPLES = 18;

      function spawnRipple(x, y, throttleMs){
        var now = performance.now();
        if (now - lastRippleTime < (throttleMs != null ? throttleMs : RIPPLE_THROTTLE)) return;
        lastRippleTime = now;
        if (ripples.length >= MAX_RIPPLES) ripples.shift();
        ripples.push({
          x:x, y:y, r:2,
          maxR:180+Math.random()*100,
          alpha:0.35,
          hue:195+Math.random()*25
        });
      }

      window.addEventListener('mousemove', function(e){
        mouseX = e.clientX; mouseY = e.clientY; hasPointer = true;
        spawnRipple(e.clientX, e.clientY, 220);
      }, {passive:true});

      window.addEventListener('mouseout', function(e){
        if (!e.relatedTarget && !e.toElement) { hasPointer = false; }
      });

      window.addEventListener('touchstart', function(e){
        if (!e.touches || !e.touches[0]) return;
        var t = e.touches[0];
        mouseX = t.clientX; mouseY = t.clientY; hasPointer = true;
        spawnRipple(t.clientX, t.clientY, 0);
      }, {passive:true});

      window.addEventListener('touchmove', function(e){
        if (!e.touches || !e.touches[0]) return;
        var t = e.touches[0];
        mouseX = t.clientX; mouseY = t.clientY;
        spawnRipple(t.clientX, t.clientY, 180);
      }, {passive:true});

      var causticTime = 0;

      function drawWater(){
        causticTime += 0.01;

        var bgGrad = ctx.createLinearGradient(0, 0, W, H);
        bgGrad.addColorStop(0, '#ffffff');
        bgGrad.addColorStop(0.5, '#f9fcff');
        bgGrad.addColorStop(1, '#f1f8ff');
        ctx.fillStyle = bgGrad;
        ctx.fillRect(0, 0, W, H);

        ctx.save();
        ctx.globalCompositeOperation = 'screen';
        for (var i = 0; i < caustics.length; i++){
          var cst = caustics[i];
          cst.x += cst.speedX; cst.y += cst.speedY;
          if (cst.x < -300) cst.x = W+300; if (cst.x > W+300) cst.x = -300;
          if (cst.y < -300) cst.y = H+300; if (cst.y > H+300) cst.y = -300;
          var r = cst.baseR + Math.sin(causticTime*1.3 + cst.phase)*40;
          var a = 0.08 + Math.sin(causticTime + cst.phase)*0.035 + 0.035;
          var rg = ctx.createRadialGradient(cst.x, cst.y, 0, cst.x, cst.y, r);
          rg.addColorStop(0, 'rgba(195,232,255,'+a+')');
          rg.addColorStop(1, 'rgba(195,232,255,0)');
          ctx.fillStyle = rg;
          ctx.beginPath();
          ctx.arc(cst.x, cst.y, r, 0, Math.PI*2);
          ctx.fill();
        }
        ctx.restore();

        ctx.save();
        ctx.strokeStyle = 'rgba(42,127,204,0.055)';
        ctx.lineWidth = 1;
        for (var wIdx = 0; wIdx < waveLines.length; wIdx++){
          var wave = waveLines[wIdx];
          wave.phase += wave.speed*0.01;
          ctx.beginPath();
          for (var x = 0; x <= W; x += 8){
            var yOff = Math.sin(x*0.012 + wave.phase)*11;
            var yy = wave.y + yOff;
            if (x === 0) ctx.moveTo(x, yy); else ctx.lineTo(x, yy);
          }
          ctx.stroke();
        }
        ctx.restore();

        for (var pi = 0; pi < particles.length; pi++){
          var pt = particles[pi];
          pt.phase += 0.02;
          if (hasPointer){
            var dx = pt.x-mouseX, dy = pt.y-mouseY;
            var dist = Math.sqrt(dx*dx+dy*dy);
            if (dist < 150 && dist > 0.001){
              var force = (150-dist)/150*0.6;
              pt.x += (dx/dist)*force; pt.y += (dy/dist)*force;
            }
          }
          pt.x += pt.vx; pt.y += pt.vy;
          if (pt.x < 0) pt.x = W; if (pt.x > W) pt.x = 0;
          if (pt.y < 0) pt.y = H; if (pt.y > H) pt.y = 0;

          var glowA = pt.alpha * (0.6 + 0.4*Math.sin(pt.phase));
          ctx.beginPath();
          ctx.fillStyle = 'rgba(90,175,225,'+(glowA*0.15)+')';
          ctx.arc(pt.x, pt.y, pt.r*2.5, 0, Math.PI*2);
          ctx.fill();

          ctx.beginPath();
          ctx.fillStyle = 'rgba(90,175,225,'+glowA+')';
          ctx.arc(pt.x, pt.y, pt.r, 0, Math.PI*2);
          ctx.fill();
        }

        for (var si = 0; si < sparkles.length; si++){
          var sp = sparkles[si];
          sp.phase += 0.025;
          if (hasPointer){
            var sdx = sp.x-mouseX, sdy = sp.y-mouseY;
            var sdist = Math.sqrt(sdx*sdx+sdy*sdy);
            if (sdist < 100 && sdist > 0.001){
              var sforce = (100-sdist)/100*0.5;
              sp.x += (sdx/sdist)*sforce; sp.y += (sdy/sdist)*sforce;
            }
          }
          sp.x += sp.driftX; sp.y += sp.driftY;
          if (sp.x < 0) sp.x = W; if (sp.x > W) sp.x = 0;
          if (sp.y < 0) sp.y = H; if (sp.y > H) sp.y = 0;

          var pulse = 0.5 + 0.5*Math.sin(sp.phase);
          var haloR = sp.size*4;
          var hg = ctx.createRadialGradient(sp.x, sp.y, 0, sp.x, sp.y, haloR);
          hg.addColorStop(0, 'rgba(210,238,255,'+(0.10*pulse)+')');
          hg.addColorStop(1, 'rgba(210,238,255,0)');
          ctx.fillStyle = hg;
          ctx.beginPath();
          ctx.arc(sp.x, sp.y, haloR, 0, Math.PI*2);
          ctx.fill();

          ctx.beginPath();
          ctx.fillStyle = 'rgba(240,250,255,'+(0.5+0.5*pulse)+')';
          ctx.arc(sp.x, sp.y, sp.size, 0, Math.PI*2);
          ctx.fill();
        }

        for (var ri = ripples.length-1; ri >= 0; ri--){
          var rp = ripples[ri];
          var progress = rp.r / rp.maxR;
          var growFactor = 1 - progress*0.5;
          rp.r += 2.2*growFactor;
          rp.alpha *= 0.985;

          if (rp.alpha < 0.015 || rp.r >= rp.maxR){ ripples.splice(ri,1); continue; }

          ctx.beginPath();
          ctx.strokeStyle = 'hsla('+rp.hue+',65%,60%,'+rp.alpha+')';
          ctx.lineWidth = 1.6;
          ctx.arc(rp.x, rp.y, rp.r, 0, Math.PI*2);
          ctx.stroke();

          ctx.beginPath();
          ctx.strokeStyle = 'hsla('+rp.hue+',70%,72%,'+(rp.alpha*0.85)+')';
          ctx.lineWidth = 1;
          ctx.arc(rp.x, rp.y, rp.r*0.55, 0, Math.PI*2);
          ctx.stroke();

          ctx.beginPath();
          ctx.strokeStyle = 'hsla('+rp.hue+',60%,80%,'+(rp.alpha*0.25)+')';
          ctx.lineWidth = 0.8;
          ctx.arc(rp.x, rp.y, rp.r*1.35, 0, Math.PI*2);
          ctx.stroke();
        }

        if (hasPointer){
          var cg = ctx.createRadialGradient(mouseX, mouseY, 0, mouseX, mouseY, 250);
          cg.addColorStop(0, 'rgba(150,210,242,0.07)');
          cg.addColorStop(1, 'rgba(150,210,242,0)');
          ctx.fillStyle = cg;
          ctx.beginPath();
          ctx.arc(mouseX, mouseY, 250, 0, Math.PI*2);
          ctx.fill();
        }

        requestAnimationFrame(drawWater);
      }
      requestAnimationFrame(drawWater);
    }
  }

  /* ---------------------------------------------------------
     2) OVERLAY MECÁNICO (engranajes / tuercas flotantes)
  --------------------------------------------------------- */
  var mechOverlay = document.querySelector('.mech-overlay');
  if (mechOverlay && !reduceMotion) {
    var gears = mechOverlay.querySelectorAll('.gear, .nut');
    var mechMouseX = 0, mechMouseY = 0, mechScrollY = 0, mechTime = 0;

    window.addEventListener('mousemove', function(e){
      mechMouseX = (e.clientX / window.innerWidth  - 0.5) * 2;
      mechMouseY = (e.clientY / window.innerHeight - 0.5) * 2;
    }, {passive:true});

    window.addEventListener('scroll', function(){
      mechScrollY = window.scrollY || document.documentElement.scrollTop;
    }, {passive:true});

    function animateMech(){
      mechTime += 1;
      gears.forEach(function(g){
        var rotSpeed = parseFloat(g.getAttribute('data-rot'))   || 0.05;
        var speed    = parseFloat(g.getAttribute('data-speed')) || 0.4;
        var rotation = mechTime * rotSpeed;
        var px = mechMouseX * 20 * speed;
        var py = mechMouseY * 20 * speed + mechScrollY * speed * 0.5;
        g.style.transform = 'translate('+px+'px,'+py+'px) rotate('+rotation+'deg)';
      });
      requestAnimationFrame(animateMech);
    }
    requestAnimationFrame(animateMech);
  }

  /* ---------------------------------------------------------
     3) CURSOR PERSONALIZADO
  --------------------------------------------------------- */
  var cursorCore = document.querySelector('.cursor-core');
  var cursorRing = document.querySelector('.cursor-ring');
  if (cursorCore && cursorRing && isFinePointer && !reduceMotion) {
    document.body.classList.add('fx-cursor-active');
    var ringX = window.innerWidth/2, ringY = window.innerHeight/2;
    var targetX = ringX, targetY = ringY;

    window.addEventListener('mousemove', function(e){
      targetX = e.clientX; targetY = e.clientY;
      cursorCore.style.left = e.clientX+'px';
      cursorCore.style.top  = e.clientY+'px';
    }, {passive:true});

    function ringLoop(){
      ringX += (targetX - ringX) * 0.12;
      ringY += (targetY - ringY) * 0.12;
      cursorRing.style.left = ringX+'px';
      cursorRing.style.top  = ringY+'px';
      requestAnimationFrame(ringLoop);
    }
    requestAnimationFrame(ringLoop);

    document.addEventListener('mouseover', function(e){
      if (e.target.closest && e.target.closest('.clickable, a, button')) {
        cursorRing.classList.add('cursor-hover');
      }
    });
    document.addEventListener('mouseout', function(e){
      if (e.target.closest && e.target.closest('.clickable, a, button')) {
        cursorRing.classList.remove('cursor-hover');
      }
    });
  }

  /* ---------------------------------------------------------
     4) TILT MAGNÉTICO + HOVER GLOW (tarjetas y botones)
  --------------------------------------------------------- */
  if (isFinePointer) {
    var glowTargets = document.querySelectorAll(
      '.neu-btn, .stat-card, .marca-card, .cert-card, .news-card, .sede-card, .contact-card, .cta-band'
    );
    glowTargets.forEach(function(el){
      el.classList.add('fx-glow');
      el.addEventListener('mousemove', function(e){
        var r = el.getBoundingClientRect();
        var mx = ((e.clientX - r.left) / r.width) * 100;
        var my = ((e.clientY - r.top) / r.height) * 100;
        el.style.setProperty('--mx', mx+'%');
        el.style.setProperty('--my', my+'%');
      });
    });

    var tiltTargets = document.querySelectorAll(
      '.stat-card, .marca-card, .cert-card, .news-card, .sede-card, .contact-card'
    );
    tiltTargets.forEach(function(el){
      el.classList.add('fx-tilt');
      el.addEventListener('mousemove', function(e){
        el.style.transition = 'none';
        var r = el.getBoundingClientRect();
        var px = (e.clientX - r.left) / r.width  - 0.5;
        var py = (e.clientY - r.top)  / r.height - 0.5;
        var rotY = Math.max(-3, Math.min(3, px*6));
        var rotX = Math.max(-3, Math.min(3, -py*6));
        el.style.transform = 'translateY(-8px) scale(1.01) rotateX('+rotX+'deg) rotateY('+rotY+'deg)';
      });
      el.addEventListener('mouseleave', function(){
        el.style.transition = 'transform .5s var(--ease)';
        el.style.transform = '';
      });
    });
  }

})();
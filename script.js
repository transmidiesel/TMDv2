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

   Robustez ante timing: todo el arranque pasa por initFX(), que
   se dispara en DOMContentLoaded si el documento aún está
   cargando, o de inmediato si ya cargó (cubre el caso de que este
   script se ejecute antes o después de que el DOM esté listo).
   Cada bloque va envuelto en try/catch por separado: si algo falla
   en el canvas de agua, NO tira abajo el overlay mecánico, el
   cursor personalizado ni el tilt/glow — antes, un solo error en
   cualquiera de las secciones detenía TODO lo que venía después
   en el mismo IIFE, lo cual explica que a veces "no cargaran las
   animaciones" en la primera visita.

   Nota: el reveal por scroll y los contadores animados YA existen
   arriba (bidireccionales, sobre .reveal y .count) — no se duplican
   aquí para no generar dos observers compitiendo por los mismos
   elementos.
   ========================================================= */
(function(){
  'use strict';

  function initFX(){

    var reduceMotion  = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var isFinePointer = window.matchMedia('(hover:hover) and (pointer:fine)').matches;
    var isDesktop     = window.matchMedia('(min-width:769px)').matches;

    /* ---------------------------------------------------------
       1) CANVAS — SOLO PARTÍCULAS
       Se quitó a pedido del usuario: fondo animado, caustics,
       líneas de oleaje, destellos especulares, ondas del cursor
       y el brillo radial del cursor. Solo quedan las partículas,
       que además se siguen alejando del cursor al pasar cerca.
    --------------------------------------------------------- */
    try {
      var canvas = document.getElementById('water-canvas');
      if (canvas && canvas.getContext) {
        var ctx = canvas.getContext('2d');
        var W = 0, H = 0;
        var mouseX = -9999, mouseY = -9999;
        var hasPointer = false;

        var resizeCanvas = function(){
          W = canvas.width  = window.innerWidth;
          H = canvas.height = window.innerHeight;
        };
        resizeCanvas();
        window.addEventListener('resize', resizeCanvas);

        if (!reduceMotion) {
          var PARTICLE_COUNT = isDesktop ? 60 : 25;

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

          window.addEventListener('mousemove', function(e){
            mouseX = e.clientX; mouseY = e.clientY; hasPointer = true;
          }, {passive:true});

          window.addEventListener('mouseout', function(e){
            if (!e.relatedTarget && !e.toElement) { hasPointer = false; }
          });

          window.addEventListener('touchstart', function(e){
            if (!e.touches || !e.touches[0]) return;
            var t = e.touches[0];
            mouseX = t.clientX; mouseY = t.clientY; hasPointer = true;
          }, {passive:true});

          window.addEventListener('touchmove', function(e){
            if (!e.touches || !e.touches[0]) return;
            var t = e.touches[0];
            mouseX = t.clientX; mouseY = t.clientY;
          }, {passive:true});

          var drawWater = function(){
            try {
              ctx.clearRect(0, 0, W, H);

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
            } catch(innerErr) {
              // Si un solo frame falla, no matamos el loop: seguimos intentando
              // en el siguiente frame en vez de dejar el canvas congelado.
            }
            requestAnimationFrame(drawWater);
          };
          requestAnimationFrame(drawWater);
        }
      }
    } catch(e) { /* el canvas no debe tumbar el resto de la capa FX */ }

    /* ---------------------------------------------------------
       2) TUERCAS ALEATORIAS (tuerca.png con profundidad/blur)
       Más grandes = más "cerca" de la pantalla = más desenfocadas.
    --------------------------------------------------------- */
    try {
      var mechOverlay = document.querySelector('.mech-overlay');
      if (mechOverlay) {
        var NUT_COUNT = isDesktop ? 16 : 8;
        var MIN_SIZE = 40, MAX_SIZE = 150;   // px
        var MAX_BLUR = 7;                    // px, en el tamaño más grande
        // Por debajo de este tamaño la tuerca queda perfectamente nítida
        // (blur 0). Por encima, el desenfoque crece con el tamaño.
        var SHARP_THRESHOLD = MIN_SIZE + (MAX_SIZE - MIN_SIZE) * 0.45;

        for (var n = 0; n < NUT_COUNT; n++){
          var size = MIN_SIZE + Math.random()*(MAX_SIZE - MIN_SIZE);
          var depth = (size - MIN_SIZE) / (MAX_SIZE - MIN_SIZE); // 0=chica, 1=grande
          var blur = size <= SHARP_THRESHOLD
            ? 0
            : ((size - SHARP_THRESHOLD) / (MAX_SIZE - SHARP_THRESHOLD)) * MAX_BLUR;
          var opacity = 0.14 + depth*0.16; // más grande, un poco más presente

          var img = document.createElement('img');
          img.src = 'tuerca.png';
          img.alt = '';
          img.className = 'nut';
          img.setAttribute('aria-hidden', 'true');
          img.setAttribute('data-rot', ((Math.random()<0.5?-1:1) * (0.03 + Math.random()*0.06)).toFixed(3));
          img.setAttribute('data-speed', (0.3 + Math.random()*0.5).toFixed(2));

          img.style.width  = size + 'px';
          img.style.height = size + 'px';
          img.style.top  = (5 + Math.random()*88) + '%';
          img.style.left = (5 + Math.random()*88) + '%';
          img.style.opacity = opacity;
          img.style.filter = 'blur(' + blur.toFixed(1) + 'px)';

          mechOverlay.appendChild(img);
        }
      }
    } catch(e) { /* si falla la generación de tuercas, seguimos con lo demás */ }

    /* ---------------------------------------------------------
       3) ANIMACIÓN DE LAS TUERCAS (giro + parallax)
       El giro tiene dos componentes: uno lento y continuo (idle),
       y otro que se dispara con la velocidad del scroll y se va
       frenando solo — así se nota claramente que giran al hacer
       scroll, sin quedar estáticas el resto del tiempo.
    --------------------------------------------------------- */
    try {
      var reduceMotion2 = reduceMotion;
      var mechOverlay2 = document.querySelector('.mech-overlay');
      if (mechOverlay2 && !reduceMotion2) {
        var mechEls = Array.prototype.map.call(
          mechOverlay2.querySelectorAll('.nut'),
          function(g){
            return {
              el: g,
              rotSpeed: parseFloat(g.getAttribute('data-rot'))   || 0.05,
              speed:    parseFloat(g.getAttribute('data-speed')) || 0.4,
              rotation: Math.random()*360
            };
          }
        );

        var mechMouseX = 0, mechMouseY = 0;
        var lastScrollY = window.scrollY || document.documentElement.scrollTop;
        var scrollYAbs = lastScrollY;
        var scrollSpin = 0; // se acumula con el scroll y decae solo

        window.addEventListener('mousemove', function(e){
          mechMouseX = (e.clientX / window.innerWidth  - 0.5) * 2;
          mechMouseY = (e.clientY / window.innerHeight - 0.5) * 2;
        }, {passive:true});

        window.addEventListener('scroll', function(){
          var y = window.scrollY || document.documentElement.scrollTop;
          var delta = y - lastScrollY;
          lastScrollY = y;
          scrollYAbs = y;
          scrollSpin += delta;
        }, {passive:true});

        var animateMech = function(){
          // decaimiento suave: si dejas de hacer scroll, el giro extra se apaga solo
          scrollSpin *= 0.92;

          mechEls.forEach(function(m){
            // giro lento constante (idle) + giro extra proporcional al scroll
            m.rotation += m.rotSpeed + scrollSpin * m.rotSpeed * 6;
            var px = mechMouseX * 20 * m.speed;
            var py = mechMouseY * 20 * m.speed + scrollYAbs * m.speed * 0.5;
            m.el.style.transform = 'translate('+px+'px,'+py+'px) rotate('+m.rotation+'deg)';
          });
          requestAnimationFrame(animateMech);
        };
        requestAnimationFrame(animateMech);
      }
    } catch(e) { /* la mecánica no debe tumbar el cursor ni el tilt/glow */ }

    /* ---------------------------------------------------------
       4) CURSOR PERSONALIZADO
    --------------------------------------------------------- */
    try {
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

        var ringLoop = function(){
          ringX += (targetX - ringX) * 0.12;
          ringY += (targetY - ringY) * 0.12;
          cursorRing.style.left = ringX+'px';
          cursorRing.style.top  = ringY+'px';
          requestAnimationFrame(ringLoop);
        };
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
    } catch(e) { /* el cursor no debe tumbar el tilt/glow */ }

    /* ---------------------------------------------------------
       5) TILT MAGNÉTICO + HOVER GLOW (tarjetas y botones)
    --------------------------------------------------------- */
    try {
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
    } catch(e) { /* último bloque: si falla, no afecta nada más */ }

  } // fin initFX

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initFX);
  } else {
    initFX();
  }

})();
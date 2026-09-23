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
// ---- Transición entre páginas ----
(function(){
  const pageContent = document.getElementById('pageContent');
  const pageTransition = document.getElementById('pageTransition');
  if (!pageContent) return;

  // Al cargar, si venimos de una transición interna, hacemos la animación de entrada
  window.addEventListener('pageshow', () => {
    if (sessionStorage.getItem('pageTransitioning') === '1') {
      sessionStorage.removeItem('pageTransitioning');

      pageContent.classList.add('is-entering');

      requestAnimationFrame(() => {
        requestAnimationFrame(() => {
          pageContent.classList.remove('is-entering');
          if (pageTransition) pageTransition.classList.remove('active');
        });
      });
    } else {
      if (pageTransition) pageTransition.classList.remove('active');
    }
  });

  // Interceptamos clics en enlaces internos
  document.addEventListener('click', (e) => {
    const link = e.target.closest('a');
    if (!link) return;

    if (link.target === '_blank') return;
    const href = link.getAttribute('href');
    if (!href) return;
    if (href.startsWith('#')) return;
    if (href.startsWith('mailto:') || href.startsWith('tel:') || href.startsWith('javascript:')) return;
    if (link.hasAttribute('download')) return;
    if (href.startsWith('http://') || href.startsWith('https://') || href.startsWith('//')) return;

    const currentPath = window.location.pathname.replace(/\/$/, '');
    const targetPath = new URL(link.href, window.location.origin).pathname.replace(/\/$/, '');
    if (currentPath === targetPath && !href.includes('#')) return;

    e.preventDefault();

    sessionStorage.setItem('pageTransitioning', '1');

    pageContent.classList.add('is-leaving');
    if (pageTransition) pageTransition.classList.add('active');

    const delay = 380; // un poco menos que .42s para que fluya
    setTimeout(() => {
      window.location.href = link.href;
    }, delay);
  });

  // Manejo del botón "atrás" del navegador (bfcache)
  window.addEventListener('pageshow', (event) => {
    if (event.persisted) {
      pageContent.classList.remove('is-leaving', 'is-entering');
      if (pageTransition) pageTransition.classList.remove('active');
    }
  });
})();
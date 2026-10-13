/* =========================================================
   SERVICIOS — Video scrubber STICKY
   La sección es muy alta. El contenido se queda pegado
   (position: sticky) mientras hacemos scroll, y solo se
   libera cuando el video termina.
   ========================================================= */
(function () {
  'use strict';

  const section  = document.getElementById('repotenciacion');
  const media    = document.getElementById('repotenciacionMedia');
  const video    = document.getElementById('repotenciacionVideo');
  const finalImg = document.getElementById('repotenciacionFinal');
  const progress = document.getElementById('repotenciacionProgress');

  if (!section || !video) return;

  /* ---------------------------------------------------------
     CONFIGURACIÓN
     ---------------------------------------------------------
     SCROLL_SCREENS: cuántas "pantallas" adicionales de scroll
     hay que hacer para reproducir TODO el video.
     - 1.0 → el video dura ~1 pantalla de scroll extra
     - 1.5 → más lento, más control
     --------------------------------------------------------- */
  const SCROLL_SCREENS = 1.3;
  const SMOOTHING      = 0.30;
  const FINAL_THRESHOLD = 0.985;

  let videoDuration = 0;
  let sectionStartY = 0;
  let sectionRangeY = 0;
  let targetTime    = 0;
  let displayTime   = 0;
  let lastApplied   = -1;
  let rafId         = null;
  let isReady       = false;

  const clamp = (v, a, b) => Math.min(Math.max(v, a), b);

  /* ---------------------------------------------------------
     Darle altura a la sección: 100vh (pin) + distancia de scroll
     --------------------------------------------------------- */
  function measure() {
    const vh = window.innerHeight;
    // Altura total = 1 pantalla visible (mientras está pinneada)
    //                + scroll necesario para recorrer el video
    const scrollDistance = vh * SCROLL_SCREENS;
    section.style.height = (vh + scrollDistance) + 'px';

    // Guardar posiciones
    const rect = section.getBoundingClientRect();
    const scrollY = window.scrollY || window.pageYOffset;
    sectionStartY = scrollY + rect.top;
    sectionRangeY = scrollDistance; // cuánto scroll hay dentro de la sección
  }

  /* ---------------------------------------------------------
     Progreso 0..1 según cuánto hemos scrolleado DENTRO de la
     sección sticky
     --------------------------------------------------------- */
  function computeProgress() {
    const scrollY = window.scrollY || window.pageYOffset;
    const local = scrollY - sectionStartY;
    return clamp(local / sectionRangeY, 0, 1);
  }

  /* ---------------------------------------------------------
     Aplicar tiempo al video
     --------------------------------------------------------- */
  function applyTime(t) {
    if (!isFinite(t) || isNaN(t)) return;
    const safe = clamp(t, 0, videoDuration - 0.001);
    if (Math.abs(safe - lastApplied) < 0.006) return;
    lastApplied = safe;
    try { video.currentTime = safe; } catch (e) {}
  }

  /* ---------------------------------------------------------
     Loop de suavizado
     --------------------------------------------------------- */
  function loop() {
    const diff = targetTime - displayTime;
    if (Math.abs(diff) > 0.0008) {
      displayTime += diff * SMOOTHING;
      applyTime(displayTime);
      rafId = requestAnimationFrame(loop);
    } else {
      displayTime = targetTime;
      applyTime(displayTime);
      rafId = null;
    }
  }
  function kick() {
    if (rafId == null) rafId = requestAnimationFrame(loop);
  }

  /* ---------------------------------------------------------
     Actualizar en cada scroll
     --------------------------------------------------------- */
  function update() {
    if (!isReady) return;

    const p = computeProgress();
    targetTime = p * videoDuration;
    kick();

    if (progress) progress.style.transform = `scaleX(${p})`;

    if (finalImg) {
      if (p >= FINAL_THRESHOLD) {
        finalImg.setAttribute('aria-hidden', 'false');
        finalImg.classList.add('visible');
      } else {
        finalImg.setAttribute('aria-hidden', 'true');
        finalImg.classList.remove('visible');
      }
    }
  }

  /* ---------------------------------------------------------
     Init
     --------------------------------------------------------- */
  function init() {
    videoDuration = video.duration;
    if (!videoDuration || !isFinite(videoDuration)) return;
    isReady = true;
    measure();
    displayTime = 0;
    targetTime  = 0;
    applyTime(0);
    update();
  }

  video.addEventListener('play', () => video.pause());
  video.pause();

  if (video.readyState >= 1) {
    init();
  } else {
    video.addEventListener('loadedmetadata', init);
  }

  window.addEventListener('scroll', update, { passive: true });
  window.addEventListener('resize', () => {
    measure();
    update();
  }, { passive: true });

  video.addEventListener('error', () => {
    if (finalImg) {
      finalImg.setAttribute('aria-hidden', 'false');
      finalImg.classList.add('visible');
    }
    section.style.height = '100vh';
  });

})();
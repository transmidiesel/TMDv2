// =========================================================
// encuestas.js
// Carga las encuestas aprobadas desde /api/encuestas.php
// y construye el carrusel marquee infinito.
// Al hacer clic en una tarjeta → abre modal con el comentario completo.
//
// IMPORTANTE: el modal se mueve al <body> al iniciar, porque
// .page-content tiene will-change:transform, lo cual rompe
// el position:fixed de los hijos (el modal aparecía a
// -2692px arriba de la pantalla).
// =========================================================

(function () {
  'use strict';

  const API = 'api/encuestas.php';
  const marquee = document.getElementById('encuestasMarquee');

  // =========================================================
  // Mover el modal al <body> ANTES de cualquier otra cosa.
  // Esta función se llama al inicio del IIFE, sin esperar al fetch.
  // =========================================================
  (function moverModalAlBodyInmediato() {
    const modal = document.getElementById('encuestaModal');
    if (!modal) return;
    if (modal.parentElement === document.body) return;
    document.body.appendChild(modal);
  })();

  // Si no existe el marquee en esta página, no seguimos
  if (!marquee) return;

  let encuestasData = [];

  // ---------- Escapar HTML ----------
  function esc(str) {
    if (str == null) return '';
    return String(str)
      .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
  }

  // ---------- Formatear fecha "Hace X días" ----------
  function formatFecha(fechaStr) {
    if (!fechaStr) return '';
    const fecha = new Date(fechaStr.replace(' ', 'T'));
    if (isNaN(fecha.getTime())) return '';

    const ahora = new Date();
    const diffMs = ahora - fecha;
    const diffDias = Math.floor(diffMs / (1000 * 60 * 60 * 24));

    if (diffDias <= 0) return 'Hoy';
    if (diffDias === 1) return 'Ayer';
    if (diffDias < 7) return `Hace ${diffDias} días`;
    if (diffDias < 30) {
      const semanas = Math.floor(diffDias / 7);
      return semanas === 1 ? 'Hace 1 semana' : `Hace ${semanas} semanas`;
    }
    if (diffDias < 365) {
      const meses = Math.floor(diffDias / 30);
      return meses === 1 ? 'Hace 1 mes' : `Hace ${meses} meses`;
    }
    const anios = Math.floor(diffDias / 365);
    return anios === 1 ? 'Hace 1 año' : `Hace ${anios} años`;
  }

  // ---------- Generar HTML de estrellas según puntaje (sobre 10) ----------
  function renderEstrellas(promedio) {
    const estrellas = (promedio / 10) * 5;
    const redondeado = Math.round(estrellas * 2) / 2;

    let html = '';
    for (let i = 1; i <= 5; i++) {
      if (redondeado >= i) {
        // Llena
        html += '<svg viewBox="0 0 24 24" fill="currentColor"><path d="m12 2 3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
      } else if (redondeado >= i - 0.5) {
        // Media
        const id = 'halfStar_' + i + '_' + Math.random().toString(36).slice(2, 7);
        html += '<svg viewBox="0 0 24 24" fill="currentColor"><defs><linearGradient id="' + id + '"><stop offset="50%" stop-color="currentColor"/><stop offset="50%" stop-color="rgba(0,0,0,0.15)"/></linearGradient></defs><path fill="url(#' + id + ')" d="m12 2 3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
      } else {
        // Vacía
        html += '<svg viewBox="0 0 24 24" fill="rgba(0,0,0,0.12)"><path d="m12 2 3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
      }
    }
    return html;
  }

  // ---------- Separar entero y decimal del puntaje ----------
  function formatPuntaje(promedio) {
    const entero = Math.floor(promedio);
    const decimal = Math.round((promedio - entero) * 10);
    if (decimal === 0) {
      return { entero: entero, decimal: '' };
    }
    return { entero: entero, decimal: '.' + decimal };
  }

  // ---------- Crear tarjeta ----------
  function crearTarjeta(enc) {
    const p = formatPuntaje(enc.promedio);
    const fechaTxt = formatFecha(enc.fecha);

    // Solo mostrar el bloque de comentario si hay texto
    const tieneComentario = enc.comentario && enc.comentario.trim() !== '';

    const bloqueComentario = tieneComentario
      ? `<p class="encuesta-card-comentario">${esc(enc.comentario)}</p>
         <span class="encuesta-card-vermas">Ver comentario completo</span>`
      : '';

    return `
      <div class="encuesta-card" data-encuesta-id="${enc.id}" tabindex="0" role="button" aria-label="Ver comentario completo de ${esc(enc.nombre)}">
        <div class="encuesta-card-head">
          <div class="encuesta-card-avatar">${esc(enc.inicial)}</div>
          <div class="encuesta-card-author">
            <h3 class="encuesta-card-nombre">${esc(enc.nombre)}</h3>
            <span class="encuesta-card-fecha">${esc(fechaTxt)}</span>
          </div>
        </div>

        <div class="encuesta-card-rating">
          <span class="encuesta-card-puntaje">
            ${p.entero}<span class="decimal">${p.decimal}</span><span class="total">/10</span>
          </span>
          <div class="encuesta-card-estrellas">${renderEstrellas(enc.promedio)}</div>
        </div>

        ${bloqueComentario}
      </div>
    `;
  }

  // ---------- Renderizar carrusel ----------
  function renderCarrusel(encuestas) {
    if (!encuestas || encuestas.length === 0) {
      marquee.innerHTML = '<p class="encuestas-loading">Aún no hay opiniones publicadas. ¡Sé el primero!</p>';
      return;
    }

    const tarjetasHtml = encuestas.map(crearTarjeta).join('');
    // Duplicar para el efecto marquee infinito
    marquee.innerHTML = tarjetasHtml + tarjetasHtml;
  }

  // =========================================================
  // MODAL — Ver comentario completo
  // =========================================================

  function abrirModal(encuestaId) {
    const enc = encuestasData.find(e => String(e.id) === String(encuestaId));
    if (!enc) return;

    const overlay  = document.getElementById('encuestaModal');
    const avatar   = document.getElementById('encuestaModalAvatar');
    const nombre   = document.getElementById('encuestaModalNombre');
    const fecha    = document.getElementById('encuestaModalFecha');
    const rating   = document.getElementById('encuestaModalRating');
    const coment   = document.getElementById('encuestaModalComentario');
    if (!overlay) return;

    const p = formatPuntaje(enc.promedio);
    const fechaTxt = formatFecha(enc.fecha);

    avatar.textContent  = enc.inicial;
    nombre.textContent  = enc.nombre;
    fecha.textContent   = fechaTxt;

    rating.innerHTML = `
      <span class="encuesta-modal-puntaje">
        ${p.entero}<span class="decimal">${p.decimal}</span><span class="total">/10</span>
      </span>
      <div class="encuesta-modal-estrellas">${renderEstrellas(enc.promedio)}</div>
    `;

    coment.textContent = (enc.comentario && enc.comentario.trim() !== '')
      ? enc.comentario
      : 'Este cliente no dejó comentario.';

    overlay.classList.add('open');
    overlay.setAttribute('aria-hidden', 'false');
    document.body.classList.add('encuesta-modal-open');
  }

  function cerrarModal() {
    const overlay = document.getElementById('encuestaModal');
    if (!overlay) return;
    overlay.classList.remove('open');
    overlay.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('encuesta-modal-open');
  }

  // Cerrar con clic en backdrop o en botón X
  document.addEventListener('click', (e) => {
    if (e.target.closest('[data-encuesta-close]')) {
      cerrarModal();
      return;
    }

    // Clic en una tarjeta → abrir modal
    const card = e.target.closest('.encuesta-card');
    if (card) {
      e.preventDefault();
      abrirModal(card.getAttribute('data-encuesta-id'));
    }
  });

  // Cerrar con Esc
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      const overlay = document.getElementById('encuestaModal');
      if (overlay && overlay.classList.contains('open')) {
        cerrarModal();
      }
    }
  });

  // =========================================================
  // INIT
  // =========================================================

  async function cargarEncuestas() {
    try {
      const res = await fetch(API, { cache: 'no-store' });
      if (!res.ok) throw new Error('HTTP ' + res.status);

      const data = await res.json();
      if (!data.ok || !Array.isArray(data.encuestas)) {
        throw new Error(data.error || 'Respuesta inválida');
      }

      encuestasData = data.encuestas;
      renderCarrusel(encuestasData);

    } catch (err) {
      console.error('[encuestas] Error:', err);
      marquee.innerHTML = '<p class="encuestas-loading">No se pudieron cargar las opiniones.</p>';
    }
  }

  // Arrancar cuando el DOM esté listo
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', cargarEncuestas);
  } else {
    cargarEncuestas();
  }

})();
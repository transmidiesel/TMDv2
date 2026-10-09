// =========================================================
// public.js — Carga contenido dinámico desde la API
// =========================================================
(function () {
  'use strict';

  const API = 'api/';

  // ---------- Utilidad: escapar HTML ----------
  function esc(str) {
    if (str == null) return '';
    return String(str)
      .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
  }

  // ---------- 1) Configuración (logos del navbar) ----------
  fetch(API + 'configuracion.php')
    .then(r => r.json())
    .then(cfg => {
      if (cfg.logo_principal) {
        const el = document.getElementById('navLogoTop');
        if (el) el.src = cfg.logo_principal;
      }
      if (cfg.logo_scrolled) {
        const el = document.getElementById('navLogoScrolled');
        if (el) el.src = cfg.logo_scrolled;
      }
    })
    .catch(err => console.warn('Config no cargada:', err));

  // ---------- 2) Marcas ----------
  fetch(API + 'marcas.php')
    .then(r => r.json())
    .then(marcas => {
      const grid = document.getElementById('marcasGrid');
      if (!grid || !Array.isArray(marcas)) return;

      grid.innerHTML = marcas.map(m => {
        const esExterno = m.enlace && /^https?:\/\//i.test(m.enlace);
        const target = esExterno ? ' target="_blank" rel="noopener"' : '';
        const href = m.enlace || '#';
        return `
          <a href="${esc(href)}"${target} class="marca-card glass reveal-pop">
            <div class="marca-logo">
              <img src="${esc(m.logo)}" alt="${esc(m.nombre)}">
            </div>
          </a>`;
      }).join('');

      if (window.revelarPops) window.revelarPops(grid);
    })
    .catch(err => console.warn('Marcas no cargadas:', err));

  // ---------- 3) Certificaciones ----------
  fetch(API + 'certificaciones.php')
    .then(r => r.json())
    .then(certs => {
      const grid = document.getElementById('certGrid');
      if (!grid || !Array.isArray(certs)) return;

      grid.innerHTML = certs.map(c => {
        const target = c.enlace_validacion ? ' target="_blank" rel="noopener"' : '';
        const href = c.enlace_validacion || '#';
        return `
          <a href="${esc(href)}"${target} class="cert-card glass reveal-pop">
            <div class="cert-logo">
              <img src="${esc(c.logo)}" alt="${esc(c.nombre)}">
            </div>
          </a>`;
      }).join('');

      if (window.revelarPops) window.revelarPops(grid);
    })
    .catch(err => console.warn('Certificaciones no cargadas:', err));

  // ---------- 4) Noticias ----------
  fetch(API + 'noticias.php')
    .then(r => r.json())
    .then(noticias => {
      const cont = document.getElementById('newsScroller');
      if (!cont || !Array.isArray(noticias)) return;

      cont.innerHTML = noticias.map((n, i) => {
        const num = String(i + 1).padStart(2, '0');
        const imagen = n.imagen
          ? `<div class="news-image"><img src="${esc(n.imagen)}" alt="${esc(n.titulo)}" loading="lazy"></div>`
          : '';
        const tag = n.enlace ? 'a' : 'div';
        const hrefAttr = n.enlace
          ? ` href="${esc(n.enlace)}" target="_blank" rel="noopener"`
          : '';
        return `
          <${tag}${hrefAttr} class="news-card glass reveal">
            ${imagen}
            <div class="news-top">
              <div class="news-icon">${num}</div>
              <h3>${esc(n.titulo)}</h3>
            </div>
            <div class="news-body">${n.descripcion || ''}</div>
          </${tag}>`;
      }).join('');

      if (window.revelarNuevos) window.revelarNuevos(cont);
    })
    .catch(err => console.warn('Noticias no cargadas:', err));

})();
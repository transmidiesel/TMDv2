/* =========================================================
   footer-certs.js
   Carga dinámicamente los certificados desde la API y los
   inyecta en el contenedor #footerCerts del pie de página.
   ========================================================= */
(function () {
  'use strict';

  function getBasePath() {
    const path = window.location.pathname;
    const match = path.match(/^(.*?\/transmiweb\/)/i);
    return match ? match[1] : '/';
  }

  function escapeHtml(str) {
    if (!str && str !== 0) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function renderCerts(certs) {
    const cont = document.getElementById('footerCerts');
    if (!cont) return;

    if (!Array.isArray(certs) || certs.length === 0) {
      cont.innerHTML = '';
      return;
    }

    const base = getBasePath();

    cont.innerHTML = certs.map(function (c) {
      const logo = escapeHtml(c.logo || '');
      const nombre = escapeHtml(c.nombre || 'Certificado');
      const enlace = escapeHtml(c.enlace_validacion || '#');
      const logoUrl = base + logo.replace(/^\/+/, '');

      return (
        '<a href="' + enlace + '" target="_blank" rel="noopener" ' +
        'class="footer-cert" aria-label="' + nombre + '" title="' + nombre + '">' +
        '<img src="' + logoUrl + '" alt="' + nombre + '" ' +
        'onerror="this.parentElement.style.display=\'none\'">' +
        '</a>'
      );
    }).join('');
  }

  document.addEventListener('DOMContentLoaded', function () {
    const apiUrl = getBasePath() + 'api/certificaciones.php';

    fetch(apiUrl, { cache: 'no-store' })
      .then(function (r) {
        if (!r.ok) throw new Error('HTTP ' + r.status);
        return r.json();
      })
      .then(renderCerts)
      .catch(function (err) {
        console.warn('[footer-certs] No se pudieron cargar los certificados:', err);
      });
  });
})();
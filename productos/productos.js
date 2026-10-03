// =========================================================
// productos.js — Catálogo con precios convertidos usando TRM
// Las tasas vienen de la BD (actualizadas por cron 1 vez/día)
// NO se llama a currencyapi desde aquí
// Incluye modal pop-up por producto + preview de descripción
// =========================================================

(function () {
  'use strict';

  const API = '../api/';

  let exchangeRates = null;
  let monedaBase    = 'USD';
  let currentCurrency = 'COP';
  let fechaActualizacion = null;
  let baseUrl = '';
  let productosData = [];

  // ---------- Utilidad: escapar HTML ----------
  function esc(str) {
    if (str == null) return '';
    return String(str)
      .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
  }

  // ---------- Utilidad: acortar texto para preview ----------
  // Corta por palabras para no partir a mitad de palabra
  function truncateText(text, maxChars) {
    if (!text) return '';
    const clean = String(text).replace(/\s+/g, ' ').trim();
    if (clean.length <= maxChars) return clean;

    // Cortar en el último espacio antes de maxChars
    const cortado = clean.substring(0, maxChars);
    const ultimoEspacio = cortado.lastIndexOf(' ');
    const resultado = ultimoEspacio > 0 ? cortado.substring(0, ultimoEspacio) : cortado;

    return resultado + '…';
  }

  // ---------- Formatear precio ----------
  function formatPrice(amount, currency) {
    const symbols = { 'COP': '$', 'USD': 'US$', 'EUR': '€', 'MXN': 'MX$' };
    const locales = { 'COP': 'es-CO', 'USD': 'en-US', 'EUR': 'de-DE', 'MXN': 'es-MX' };

    const symbol = symbols[currency] || currency;
    const locale = locales[currency] || 'es-CO';

    try {
      return symbol + ' ' + new Intl.NumberFormat(locale, {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
      }).format(amount);
    } catch (e) {
      return symbol + ' ' + amount.toLocaleString();
    }
  }

  // ---------- Convertir precio ----------
  function convertPrice(priceBase, targetCurrency) {
    if (targetCurrency === monedaBase) return priceBase;
    if (!exchangeRates || !exchangeRates[targetCurrency]) return priceBase;
    return priceBase * exchangeRates[targetCurrency];
  }

  // ---------- Construir URL de imagen ----------
  function buildImageUrl(imagen) {
    if (!imagen) return '';
    if (/^https?:\/\//i.test(imagen)) return imagen;
    if (imagen.startsWith('/')) return imagen;
    if (baseUrl) return baseUrl + imagen;
    return '../' + imagen;
  }

  // ---------- Crear tarjeta (compacta, con preview) ----------
  function createProductCard(product) {
    const precioBase     = parseFloat(product.precio_usd) || 0;
    const precioConvert  = convertPrice(precioBase, currentCurrency);
    const precioFormateado = formatPrice(precioConvert, currentCurrency);

    const imgUrl = buildImageUrl(product.imagen);

    // Preview: máximo 140 caracteres (aprox 2-3 líneas según ancho)
    const preview = truncateText(product.descripcion || '', 140);

    return `
      <div class="producto-card glass reveal" data-producto-id="${esc(product.id)}">
        <div class="producto-imagen">
          ${imgUrl
            ? `<img src="${esc(imgUrl)}" alt="${esc(product.nombre)}" loading="lazy"
                    onerror="this.parentNode.innerHTML='<div class=\\'no-img\\'>Imagen no disponible</div>';">
               `
            : '<div class="no-img">Sin imagen</div>'
          }
        </div>
        <div class="producto-info">
          <h3 class="producto-nombre">${esc(product.nombre)}</h3>
          ${preview
            ? `<p class="producto-preview">${esc(preview)}</p>`
            : ''
          }
          <div class="producto-precio" data-precio-usd="${precioBase}">
            <span class="precio-valor">${precioFormateado}</span>
            <span class="precio-moneda">${currentCurrency}</span>
          </div>
          <div class="producto-acciones">
            <button type="button" class="producto-btn-ver" data-ver-producto="${esc(product.id)}">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="3"></circle>
                <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"></path>
              </svg>
              Ver más
            </button>
            <a href="https://wa.me/573168775212?text=${encodeURIComponent('Hola, me interesa el producto: ' + product.nombre)}"
               target="_blank"
               class="producto-btn">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
              </svg>
              Contáctanos
            </a>
          </div>
        </div>
      </div>
    `;
  }

  // ---------- Renderizar ----------
  function renderProducts(products) {
    const grid = document.getElementById('productosGrid');
    if (!grid) return;

    productosData = products || [];

    if (!products || products.length === 0) {
      grid.innerHTML = '<p class="productos-empty">No hay productos disponibles por el momento.</p>';
      return;
    }

    grid.innerHTML = products.map(createProductCard).join('');

    if (window.revelarNuevos) window.revelarNuevos(grid);
  }

  // ---------- Actualizar precios al cambiar moneda ----------
  function updatePrices() {
    document.querySelectorAll('.producto-precio').forEach(el => {
      const precioBase = parseFloat(el.dataset.precioUsd);
      if (isNaN(precioBase)) return;

      const precioConvert    = convertPrice(precioBase, currentCurrency);
      const precioFormateado = formatPrice(precioConvert, currentCurrency);

      el.querySelector('.precio-valor').textContent  = precioFormateado;
      el.querySelector('.precio-moneda').textContent = currentCurrency;
    });

    const modal = document.querySelector('.producto-modal-overlay.open');
    if (modal) {
      const priceEl = modal.querySelector('.producto-modal-precio');
      if (priceEl) {
        const precioBase = parseFloat(priceEl.dataset.precioUsd);
        if (!isNaN(precioBase)) {
          const precioConvert    = convertPrice(precioBase, currentCurrency);
          const precioFormateado = formatPrice(precioConvert, currentCurrency);
          priceEl.querySelector('.precio-valor').textContent  = precioFormateado;
          priceEl.querySelector('.precio-moneda').textContent = currentCurrency;
        }
      }
    }
  }

  // ---------- Selector de moneda ----------
  function createCurrencySelector() {
    const disponibles = ['COP', 'USD', 'EUR', 'MXN'];
    const nombres = {
      'COP': 'COP - Peso Colombiano',
      'USD': 'USD - Dólar',
      'EUR': 'EUR - Euro',
      'MXN': 'MXN - Peso Mexicano'
    };

    const opciones = disponibles.map(m =>
      `<option value="${m}" ${m === currentCurrency ? 'selected' : ''}>${nombres[m]}</option>`
    ).join('');

    const selector = document.createElement('div');
    selector.className = 'currency-selector';
    selector.innerHTML = `
      <label for="currencySelect">Moneda:</label>
      <select id="currencySelect">${opciones}</select>
      ${fechaActualizacion ? `<small class="trm-info">TRM actualizada: ${esc(fechaActualizacion)}</small>` : ''}
    `;

    const sectionHead = document.querySelector('#productosGrid')?.closest('section')?.querySelector('.section-head');
    if (sectionHead) sectionHead.appendChild(selector);

    document.getElementById('currencySelect')?.addEventListener('change', (e) => {
      currentCurrency = e.target.value;
      updatePrices();
    });
  }

  // =========================================================
  // MODAL DE PRODUCTO
  // =========================================================

  function createModal() {
    if (document.querySelector('.producto-modal-overlay')) return;

    const overlay = document.createElement('div');
    overlay.className = 'producto-modal-overlay';
    overlay.innerHTML = `
      <div class="producto-modal-backdrop" data-modal-close></div>
      <div class="producto-modal" role="dialog" aria-modal="true">
        <button type="button" class="producto-modal-close" data-modal-close aria-label="Cerrar">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
            <path d="M18 6 6 18M6 6l12 12"/>
          </svg>
        </button>
        <div class="producto-modal-inner">
          <div class="producto-modal-imagen">
            <img src="" alt="">
          </div>
          <div class="producto-modal-content">
            <span class="producto-modal-kicker"></span>
            <h2 class="producto-modal-titulo"></h2>
            <div class="producto-modal-precio" data-precio-usd="">
              <span class="precio-valor"></span>
              <span class="precio-moneda"></span>
            </div>
            <div class="producto-modal-descripcion"></div>
            <a href="#" target="_blank" class="producto-modal-btn-whatsapp">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
              </svg>
              Contáctanos por WhatsApp
            </a>
          </div>
        </div>
      </div>
    `;

    document.body.appendChild(overlay);

    overlay.addEventListener('click', (e) => {
      if (e.target.closest('[data-modal-close]')) {
        closeModal();
      }
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && overlay.classList.contains('open')) {
        closeModal();
      }
    });
  }

  function openModal(productoId, triggerEl) {
    const producto = productosData.find(p => String(p.id) === String(productoId));
    if (!producto) return;

    createModal();
    const overlay = document.querySelector('.producto-modal-overlay');
    const modal   = overlay.querySelector('.producto-modal');

    const rect = triggerEl.getBoundingClientRect();
    const originX = rect.left + rect.width / 2;
    const originY = rect.top + rect.height / 2;

    const modalCenterX = window.innerWidth / 2;
    const modalCenterY = window.innerHeight / 2;

    const deltaX = originX - modalCenterX;
    const deltaY = originY - modalCenterY;

    modal.style.transition = 'none';
    modal.style.transform = `translate(${deltaX}px, ${deltaY}px) scale(0.35)`;
    modal.style.opacity = '0';

    const imgUrl = buildImageUrl(producto.imagen);

    overlay.querySelector('.producto-modal-imagen').innerHTML = imgUrl
      ? `<img src="${esc(imgUrl)}" alt="${esc(producto.nombre)}"
              onerror="this.parentNode.innerHTML='<div class=\\'no-img\\'>Imagen no disponible</div>';">`
      : '<div class="no-img">Sin imagen</div>';

    overlay.querySelector('.producto-modal-kicker').textContent = producto.categoria || 'Producto';
    overlay.querySelector('.producto-modal-titulo').textContent = producto.nombre;

    const precioBase = parseFloat(producto.precio_usd) || 0;
    const precioConvert = convertPrice(precioBase, currentCurrency);
    const precioFmt = formatPrice(precioConvert, currentCurrency);

    const priceEl = overlay.querySelector('.producto-modal-precio');
    priceEl.dataset.precioUsd = precioBase;
    priceEl.querySelector('.precio-valor').textContent = precioFmt;
    priceEl.querySelector('.precio-moneda').textContent = currentCurrency;

    overlay.querySelector('.producto-modal-descripcion').textContent = producto.descripcion || 'Sin descripción disponible.';

    const waBtn = overlay.querySelector('.producto-modal-btn-whatsapp');
    waBtn.href = 'https://wa.me/573168775212?text=' + encodeURIComponent('Hola, me interesa el producto: ' + producto.nombre);

    overlay.classList.add('open');
    document.body.classList.add('modal-open');

    void modal.offsetWidth;
    modal.style.transition = '';
    modal.style.transform = 'translate(0, 0) scale(1)';
    modal.style.opacity = '1';

    setTimeout(() => {
      overlay.querySelector('.producto-modal-close')?.focus();
    }, 100);
  }

  function closeModal() {
    const overlay = document.querySelector('.producto-modal-overlay');
    if (!overlay) return;

    const modal = overlay.querySelector('.producto-modal');

    modal.style.transform = 'translate(0, 40px) scale(0.5)';
    modal.style.opacity = '0';

    setTimeout(() => {
      overlay.classList.remove('open');
      document.body.classList.remove('modal-open');
      modal.style.transform = '';
      modal.style.opacity = '';
    }, 350);
  }

  function setupModalListeners() {
    document.addEventListener('click', (e) => {
      const btn = e.target.closest('[data-ver-producto]');
      if (btn) {
        e.preventDefault();
        const id = btn.getAttribute('data-ver-producto');
        openModal(id, btn.closest('.producto-card'));
      }
    });
  }

  // ---------- Cargar datos ----------
  async function loadData() {
    try {
      const response = await fetch(API + 'productos.php', { cache: 'no-store' });
      if (!response.ok) throw new Error('HTTP ' + response.status);

      const data = await response.json();

      if (data.tasas)             exchangeRates = data.tasas;
      if (data.moneda_base)       monedaBase    = data.moneda_base;
      if (data.fecha_actualizada) fechaActualizacion = data.fecha_actualizada;
      if (data.base_url)          baseUrl       = data.base_url;

      renderProducts(data.productos || []);

    } catch (error) {
      console.warn('Error cargando productos:', error);

      exchangeRates = { COP: 4000, EUR: 0.92, MXN: 17.15 };
      renderProducts([
        { id: 1, nombre: 'BOX COOLER', descripcion: 'Enfriador de aceite para motores marinos e industriales de alta eficiencia.', imagen: 'productos/box-cooler.png', precio_usd: 450, categoria: 'naval' },
        { id: 2, nombre: 'BUJE BAQUELITA NO METÁLICO', descripcion: 'Buje de baquelita para ejes de transmisión naval resistente a la corrosión.', imagen: 'productos/buje-baquelita.png', precio_usd: 85, categoria: 'naval' },
        { id: 3, nombre: 'BUJE CAUCHO BRONCE', descripcion: 'Buje de caucho con inserto de bronce para alta resistencia y durabilidad.', imagen: 'productos/buje-caucho-bronce.png', precio_usd: 120, categoria: 'naval' },
        { id: 4, nombre: 'BUJE CAUCHO BRONCE CON BRIDA O FLANGE', descripcion: 'Buje con brida de sujeción para aplicaciones navales de alto rendimiento.', imagen: 'productos/buje-caucho-brida.png', precio_usd: 165, categoria: 'naval' },
        { id: 5, nombre: 'CARGADOR DE BATERÍAS', descripcion: 'Cargador de baterías industriales con múltiples salidas y protección inteligente.', imagen: 'productos/cargador-baterias.png', precio_usd: 320, categoria: 'industrial' }
      ]);
    }
  }

  // ---------- Init ----------
  function init() {
    createCurrencySelector();
    createModal();
    setupModalListeners();
    loadData();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();
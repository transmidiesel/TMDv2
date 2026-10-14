<?php
// =========================================================
// empleados/index.php
// =========================================================
$base      = '../';
$pageTitle = 'Empleados — Transmidiesel S.A.S';

require __DIR__ . '/../partials/head.php';
require __DIR__ . '/../partials/navbar.php';
?>

<div class="page-content" id="pageContent">

<div class="bg-mesh"></div>
<div class="grain"></div>
<div class="cursor-glow" id="cursorGlow"></div>

<!-- ================= HERO ================= -->
<section class="page-hero" style="padding-top:120px;padding-bottom:20px;">
  <div class="wrap">
    <span class="kicker">Recursos Humanos</span>
    <h1>Empleados</h1>
    <p>Espacio informativo para nuestro equipo. Ingresa tu número de cédula para consultar el reglamento interno de trabajo.</p>
  </div>
</section>

<!-- ================= VALIDACIÓN ================= -->
<section class="tint" style="padding-top:28px;padding-bottom:28px;">
  <div class="wrap">
    <div class="empleado-acceso-grid">

      <!-- Columna Izquierda: título, descripción e información -->
      <div class="empleado-acceso-info reveal">
        <span class="kicker">Acceso</span>
        <h2>Consulta el Reglamento Interno</h2>
        <p>Ingresa tu número de cédula. Si estás registrado en nuestra base de datos, podrás abrir el reglamento interno de trabajo en formato PDF.</p>

        <ul class="empleado-acceso-list">
          <li>
            <span class="empleado-check">✓</span>
            Acceso restringido a empleados registrados.
          </li>
          <li>
            <span class="empleado-check">✓</span>
            Tu consulta queda registrada con fecha y hora.
          </li>
          <li>
            <span class="empleado-check">✓</span>
            Si no estás registrado, contacta a Recursos Humanos.
          </li>
        </ul>
      </div>

      <!-- Columna Derecha: formulario -->
      <div class="empleado-acceso-form glass reveal">
        <h3>Validar identidad</h3>
        <p class="empleado-form-sub">Solo necesitas tu número de cédula.</p>

        <form id="formEmpleado" autocomplete="off" novalidate>
          <div class="form-group" style="margin-bottom:18px;">
            <label for="cedula" class="form-label">Número de cédula <span class="form-req">*</span></label>
            <input type="text" id="cedula" name="cedula" class="form-input" placeholder="Ej. 1234567890" inputmode="numeric" required>
          </div>
          <div class="form-actions" style="justify-content:center;margin-top:0;">
            <button type="submit" class="neu-btn primary magnetic" id="btnValidar" style="width:100%;">
              <span id="btnValidarText">Validar y abrir reglamento</span>
            </button>
          </div>
          <div id="formEmpleadoStatus" class="form-status" style="display:none;" role="alert"></div>
        </form>
      </div>

    </div>
  </div>
</section>

<!-- ================= CTA ================= -->
<section id="contacto" style="padding-top:28px;padding-bottom:48px;">
  <div class="wrap">
    <div class="cta-band glass reveal" style="padding:36px 30px;">
      <h2>¿Necesitas más información?</h2>
      <p>Escríbenos y con gusto te asesoramos.</p>
      <a href="../contactenos/" class="neu-btn primary magnetic">Ir a Contáctenos</a>
    </div>
  </div>
</section>

</div>
<!-- ↑ cierra .page-content -->

<?php require __DIR__ . '/../partials/footer.php'; ?>
<?php require __DIR__ . '/../partials/whatsapp.php'; ?>

<!-- ================= MODAL DE AGRADECIMIENTO ================= -->
<div class="empleado-modal-overlay" id="empleadoModal">
  <div class="empleado-modal glass" role="dialog" aria-modal="true" aria-labelledby="empleadoModalTitle">
    <div class="empleado-modal-check" aria-hidden="true">
      <svg viewBox="0 0 52 52" fill="none">
        <circle cx="26" cy="26" r="24" stroke="currentColor" stroke-width="2"/>
        <path d="M14 27l8 8 16-16" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </div>
    <h3 id="empleadoModalTitle">¡Bienvenido!</h3>
    <p id="empleadoModalMsg">Gracias por consultar el reglamento.</p>
    <div class="empleado-modal-actions">
      <a href="#" id="empleadoModalLink" target="_blank" rel="noopener" class="neu-btn primary magnetic">Abrir reglamento</a>
      <button type="button" id="empleadoModalClose" class="neu-btn magnetic">Cerrar</button>
    </div>
  </div>
</div>

<div class="cursor-core" aria-hidden="true"></div>
<div class="cursor-ring" aria-hidden="true"></div>

<?php
$extraScripts = [];
require __DIR__ . '/../partials/scripts.php';
?>

<!-- ================= SCRIPT DE VALIDACIÓN DE EMPLEADO ================= -->
<script>
(function(){

  // =====================================================================
  // CONFIGURACIÓN: URL de tu Web App de Google Apps Script
  // =====================================================================
  const APPS_SCRIPT_URL = "https://script.google.com/macros/s/AKfycbzScfIYP6Cbrm6PndlmonpyBE0jWCJXNDI2w_D99B9Bba0JtS_L3PKWxd5Qxt38r-i5gw/exec";

  const form     = document.getElementById('formEmpleado');
  const input    = document.getElementById('cedula');
  const btn      = document.getElementById('btnValidar');
  const btnText  = document.getElementById('btnValidarText');
  const statusEl = document.getElementById('formEmpleadoStatus');

  const modal      = document.getElementById('empleadoModal');
  const modalMsg   = document.getElementById('empleadoModalMsg');
  const modalLink  = document.getElementById('empleadoModalLink');
  const modalClose = document.getElementById('empleadoModalClose');

  // ---------- Obtener IP pública del usuario ----------
  async function obtenerIP() {
    try {
      const r = await fetch('https://api.ipify.org?format=json', { cache: 'no-store' });
      const d = await r.json();
      return d.ip || 'No disponible';
    } catch (e) {
      return 'No disponible';
    }
  }

  // ---------- Mostrar estado ----------
  function setStatus(type, html) {
    statusEl.className = 'form-status ' + type;
    const icon = type === 'success' ? '✅' : '⚠️';
    statusEl.innerHTML = '<span class="ic-status">' + icon + '</span><div>' + html + '</div>';
    statusEl.style.display = 'flex';
  }
  function clearStatus() {
    statusEl.style.display = 'none';
    statusEl.innerHTML = '';
  }

  // ---------- Abrir modal ----------
  function abrirModal(nombre, apellido, urlReglamento) {
    modalMsg.innerHTML = 'Hola <strong>' + nombre + ' ' + apellido + '</strong>, gracias por consultar el reglamento interno de trabajo.';
    modalLink.href = urlReglamento || '#';
    modal.classList.add('open');
    document.body.style.overflow = 'hidden';
  }
  function cerrarModal() {
    modal.classList.remove('open');
    document.body.style.overflow = '';
  }
  modalClose.addEventListener('click', cerrarModal);
  modal.addEventListener('click', function(e){
    if (e.target === modal) cerrarModal();
  });
  document.addEventListener('keydown', function(e){
    if (e.key === 'Escape' && modal.classList.contains('open')) cerrarModal();
  });

  // ---------- Envío del formulario ----------
  form.addEventListener('submit', async function(e){
    e.preventDefault();

    const cedula = (input.value || '').replace(/\D/g, '');

    if (!cedula) {
      setStatus('error', 'Por favor ingresa tu número de cédula.');
      return;
    }

    btn.disabled = true;
    btn.style.opacity = '0.7';
    btnText.textContent = 'Validando...';
    clearStatus();

    try {
      const ip = await obtenerIP();
      const userAgent = navigator.userAgent || '';

      const payload = {
        cedula: cedula,
        ip: ip,
        userAgent: userAgent
      };

      let data = null;
      try {
        const r2 = await fetch(APPS_SCRIPT_URL, {
          method: 'POST',
          headers: { 'Content-Type': 'text/plain;charset=utf-8' },
          body: JSON.stringify(payload)
        });
        data = await r2.json();
      } catch (e2) {
        data = null;
      }

      btn.disabled = false;
      btn.style.opacity = '1';
      btnText.textContent = 'Validar y abrir reglamento';

      if (data && data.status === 'ok') {
        abrirModal(data.nombre, data.apellido, data.reglamento);
        setStatus('success', 'Cédula verificada. Abriendo reglamento…');
        input.value = '';
      } else if (data && data.status === 'error') {
        setStatus('error', data.message || 'La cédula no está registrada.');
      } else {
        setStatus('error', 'No se pudo validar la cédula. Inténtalo de nuevo.');
      }

    } catch (err) {
      console.error('Error validando cédula:', err);
      btn.disabled = false;
      btn.style.opacity = '1';
      btnText.textContent = 'Validar y abrir reglamento';
      setStatus('error', 'Hubo un problema de conexión. Inténtalo de nuevo más tarde.');
    }
  });

})();
</script>
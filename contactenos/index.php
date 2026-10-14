<?php
// =========================================================
// contactenos/index.php
// =========================================================
$base      = '../';
$pageTitle = 'Contáctenos — Transmidiesel S.A.S';

require __DIR__ . '/../partials/head.php';
require __DIR__ . '/../partials/navbar.php';
?>

<div class="page-content" id="pageContent">

<div class="bg-mesh"></div>
<div class="grain"></div>
<div class="cursor-glow" id="cursorGlow"></div>

<!-- ================= HERO ================= -->
<section class="page-hero contact-hero">
  <div class="wrap">
    <span class="kicker">Atención y Asesoría Especializada</span>
    <h1>Contáctenos</h1>
    <p>Estamos a tu disposición para asesorarte, cotizar maquinaria, partes y repuestos, o coordinar servicios de mantenimiento técnico en toda Colombia.</p>
  </div>
</section>

<!-- ================= FORMULARIO + HORARIOS ================= -->
<section style="padding-top:10px;padding-bottom:50px;">
  <div class="wrap">
    <div class="contact-main-grid">

      <!-- Columna Izquierda: Formulario conectado a Google Sheets y Correo -->
      <div class="contact-form-card glass reveal">
        <h2>Envíanos un mensaje</h2>
        <p class="form-desc">Diligencia tus datos de contacto y cuéntanos qué producto, repuesto o servicio requieres. Te responderemos a la brevedad.</p>

        <form id="contactForm" novalidate>
          <div class="form-row">
            <div class="form-group">
              <label for="nombre" class="form-label">Nombre completo <span class="form-req">*</span></label>
              <input type="text" id="nombre" name="nombre" class="form-input" placeholder="Ej. Juan Pérez" required>
            </div>
            <div class="form-group">
              <label for="correo" class="form-label">Correo electrónico <span class="form-req">*</span></label>
              <input type="email" id="correo" name="correo" class="form-input" placeholder="Ej. juan.perez@empresa.com" required>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="telefono" class="form-label">Teléfono / Celular <span class="form-req">*</span></label>
              <input type="tel" id="telefono" name="telefono" class="form-input" placeholder="Ej. +57 316 000 0000" required>
            </div>
            <div class="form-group">
              <label for="asunto" class="form-label">Asunto / Tipo de consulta</label>
              <select id="asunto" name="asunto" class="form-select">
                <option value="Cotización de repuestos o equipos">Cotización de repuestos o equipos</option>
                <option value="Mantenimiento y taller de servicios">Mantenimiento y taller de servicios</option>
                <option value="Consulta sobre marcas distribuidas">Consulta sobre marcas distribuidas</option>
                <option value="Asesoría técnica industrial o marina">Asesoría técnica industrial o marina</option>
                <option value="Otra consulta">Otra consulta</option>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label for="mensaje" class="form-label">Mensaje o consulta <span class="form-req">*</span></label>
            <textarea id="mensaje" name="mensaje" class="form-textarea" rows="5" placeholder="Escribe aquí los detalles de tu consulta, equipo, referencia o requerimiento..." required></textarea>
          </div>

          <div class="form-actions">
            <button type="submit" class="neu-btn primary magnetic send-btn" id="submitBtn">
              <span class="send-icon" aria-hidden="true">
                <svg class="ic-envelope" viewBox="0 0 32 32" fill="none">
                  <rect x="3" y="9" width="26" height="18" rx="3" stroke="currentColor" stroke-width="2"/>
                  <path class="envelope-flap" d="M3 11l13 9 13-9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="speed-lines">
                  <span></span><span></span><span></span>
                </span>
                <svg class="ic-check" viewBox="0 0 32 32" fill="none">
                  <path d="M6 17l6 6 14-14" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </span>
              <span id="btnText">Enviar mensaje</span>
            </button>
            <span style="font-size:12.5px;color:var(--metal-light);">Tus datos están protegidos y serán tratados con confidencialidad.</span>
          </div>

          <div id="formStatus" class="form-status" style="display:none;" role="alert"></div>
        </form>
      </div>

      <!-- Columna Derecha: Horarios de atención y canales directos -->
      <div class="contact-side-stack reveal">

        <!-- Tarjeta de Horario de Atención -->
        <div class="hours-card glass">
          <div class="hours-head">
            <svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <circle cx="12" cy="12" r="10"/>
              <polyline points="12 6 12 12 16 14"/>
            </svg>
            <h3>Horario de Atención</h3>
          </div>

          <div class="hours-list">
            <div class="hours-item">
              <span class="hours-day">
                <span class="hours-dot-online"></span> Lunes a Viernes:
              </span>
              <span class="hours-time">7:30 a.m. a 12:00 p.m. / 1:00 p.m. a 5:00 p.m.</span>
            </div>

            <div class="hours-item">
              <span class="hours-day">
                <span class="hours-dot-online"></span> Sábado:
              </span>
              <span class="hours-time">8:00 a.m. a 12:00 p.m.</span>
            </div>

            <div class="hours-item">
              <span class="hours-day" style="color:var(--metal-light);">
                Domingos y Festivos:
              </span>
              <span class="hours-time" style="background:rgba(120,140,160,0.06);border-left-color:var(--metal-light);color:var(--metal-light);">
                Cerrado
              </span>
            </div>
          </div>

          <div class="hours-note">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            <span>Atención telefónica y WhatsApp disponible en horario laboral.</span>
          </div>
        </div>

        <!-- Tarjeta de Canales Rápidos -->
        <div class="quick-channels-card glass">
          <h4>Canales Directos</h4>

          <a href="tel:+573168775212" class="channel-row">
            <div class="channel-ic">
              <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            </div>
            <div class="channel-info">
              <span class="channel-title">Línea Nacional</span>
              <span class="channel-val">+57 316 877 5212</span>
            </div>
          </a>

          <a href="https://wa.me/573168775212" target="_blank" rel="noopener" class="channel-row">
            <div class="channel-ic" style="background:rgba(37,211,102,0.12);color:#25d366;">
              <svg viewBox="0 0 32 32" width="22" height="22" fill="currentColor"><path d="M16 3C9 3 3.3 8.6 3.3 15.5c0 2.6.8 5 2.1 7L3 29l6.7-2.3c1.9 1 4.1 1.6 6.3 1.6 7 0 12.7-5.6 12.7-12.5S23 3 16 3z"/><path d="M12.1 10.4c-.3-.7-.6-.7-.9-.7h-.7c-.3 0-.7.1-1 .5-.3.4-1.3 1.2-1.3 3s1.3 3.5 1.5 3.7c.2.3 2.5 4 6.2 5.5 3 1.2 3.6 1 4.3.9.7-.1 2.1-.8 2.4-1.6.3-.8.3-1.5.2-1.6-.1-.2-.3-.3-.7-.5s-2.1-1.1-2.4-1.2c-.3-.1-.6-.2-.8.2-.2.4-.9 1.2-1.1 1.4-.2.2-.4.3-.7.1-.4-.2-1.5-.6-2.8-1.8-1-1-1.7-2.1-1.9-2.5-.2-.4 0-.6.2-.8l.5-.6c.1-.2.2-.3.3-.5.1-.2 0-.4 0-.6 0-.2-.7-1.9-1-2.7z" fill="#ffffff"/></svg>
            </div>
            <div class="channel-info">
              <span class="channel-title">WhatsApp Corporativo</span>
              <span class="channel-val">Iniciar chat inmediato</span>
            </div>
          </a>
        </div>

      </div>

    </div>
  </div>
</section>

<!-- ================= SEDES CON MAPAS ================= -->
<section class="tint" style="padding-top:60px;">
  <div class="wrap">
    <div class="section-head reveal">
      <span class="kicker">Presencia Nacional</span>
      <h2>Nuestras Sedes</h2>
      <p>Visítanos en nuestras instalaciones principales en Cali y Cartagena de Indias.</p>
    </div>

    <div class="sede-grid">
      <!-- Sede Cali -->
      <div class="sede-card glass reveal">
        <div class="sede-info">
          <span class="sede-kicker">Sede Principal — Cali</span>
          <h3>Transmidiesel S.A.S</h3>
          <div class="sede-line"><span class="ic">📍</span> Carrera 1 # 50N-89, Barrio Evaristo García</div>
          <div class="sede-line"><span class="ic">☎</span> Línea Nacional: +57 316 877 5212</div>
        </div>
        <div class="sede-map">
          <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3982.480851922681!2d-76.50763982416072!3d3.475367796499033!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e30a62cfff209bb%3A0xbefb05c4becedb21!2sTransmidiesel%20Cali!5e0!3m2!1ses!2sco!4v1789768178789!5m2!1ses!2sco" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin" title="Mapa Sede Cali"></iframe>
        </div>
      </div>

      <!-- Sede Cartagena -->
      <div class="sede-card glass reveal">
        <div class="sede-info">
          <span class="sede-kicker">Agencia Caribe — Cartagena</span>
          <h3>Transmidiesel S.A.S</h3>
          <div class="sede-line"><span class="ic">📍</span> Diagonal 22 # 37 – 54, Av Crisanto Luque — Barrio El Bosque</div>
          <div class="sede-line"><span class="ic">☎</span> Contacto: +57 316 877 5207</div>
        </div>
        <div class="sede-map">
          <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d245.26278235061307!2d-75.52076325154513!3d10.405325076502088!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8ef6258ebc8a871b%3A0xaf5182d52c8b5cd9!2sTransmidiesel%20Cartagena!5e0!3m2!1ses!2sco!4v1790352927681!5m2!1ses!2sco" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin" title="Mapa Sede Cartagena"></iframe>
        </div>
      </div>
    </div>

    <!-- Contactos de asesores -->
    <div class="contact-grid">
      <div class="contact-card glass reveal">
        <div class="role">Administración · Cali</div>
        <div class="person">Diana Marcela Ordoñez</div>
        <div class="phone"><a href="tel:+573155735099">+57 315 573 5099</a></div>
      </div>
      <div class="contact-card glass reveal">
        <div class="role">Comercial · Cali</div>
        <div class="person">Oscar Asesor Comercial</div>
        <div class="phone"><a href="tel:+573165220137">+57 316 522 0137</a></div>
      </div>
      <div class="contact-card glass reveal">
        <div class="role">Administración · Cartagena</div>
        <div class="person">Manuel Blanco</div>
        <div class="phone"><a href="tel:+573168775207">+57 316 877 5207</a></div>
      </div>
      <div class="contact-card glass reveal">
        <div class="role">Comercial · Cartagena</div>
        <div class="person">Hebert Caraballo</div>
        <div class="phone"><a href="tel:+573168775201">+57 316 877 5201</a></div>
      </div>
    </div>
  </div>
</section>

</div>
<!-- ↑ cierra .page-content -->

<?php require __DIR__ . '/../partials/footer.php'; ?>
<?php require __DIR__ . '/../partials/whatsapp.php'; ?>

<div class="cursor-core" aria-hidden="true"></div>
<div class="cursor-ring" aria-hidden="true"></div>

<?php
$extraScripts = [];
require __DIR__ . '/../partials/scripts.php';
?>

<!-- ================= SCRIPT DEL FORMULARIO CON GOOGLE APPS SCRIPT ================= -->
<script>
  // =========================================================================
  // CONFIGURACIÓN DE CONEXIÓN CON GOOGLE APPS SCRIPT
  // =========================================================================
  const APPS_SCRIPT_URL = "https://script.google.com/macros/s/AKfycbwTtIIoIoQ5P7vEWPH4T6ybujcnbQAE1SMmQ1D8hmL3PNjdc5ae5vttzKngxOdNjSDo/exec";

  const form = document.getElementById('contactForm');
  const submitBtn = document.getElementById('submitBtn');
  const btnText = document.getElementById('btnText');
  const formStatus = document.getElementById('formStatus');

  form.addEventListener('submit', async function(e) {
    e.preventDefault();

    const nombre = document.getElementById('nombre').value.trim();
    const correo = document.getElementById('correo').value.trim();
    const telefono = document.getElementById('telefono').value.trim();
    const asunto = document.getElementById('asunto').value;
    const mensaje = document.getElementById('mensaje').value.trim();

    if (!nombre || !correo || !telefono || !mensaje) {
      showStatus('error', 'Por favor diligencia todos los campos obligatorios (*) antes de enviar.');
      return;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(correo)) {
      showStatus('error', 'Por favor ingresa un correo electrónico válido.');
      return;
    }

    setButtonState('sending');
    formStatus.style.display = 'none';

    const payload = {
      nombre: nombre,
      correo: correo,
      telefono: telefono,
      asunto: asunto,
      mensaje: mensaje
    };

    try {
      await fetch(APPS_SCRIPT_URL, {
        method: 'POST',
        mode: 'no-cors',
        headers: {
          'Content-Type': 'text/plain;charset=utf-8'
        },
        body: JSON.stringify(payload)
      });

      setButtonState('sent');
      showStatus('success', '<strong>¡Tu mensaje ha sido enviado con éxito!</strong><br>Gracias por contactar a Transmidiesel S.A.S. Un asesor comercial revisará tu solicitud y te responderá a la brevedad.');
      form.reset();

    } catch (err) {
      console.error('Error al enviar formulario:', err);
      setButtonState('error');
      showStatus('error', 'Hubo un inconveniente al conectar con el servidor. Por favor intenta nuevamente o comunícate directamente por WhatsApp o a nuestra Línea Nacional: +57 316 877 5212.');
    }
  });

  function setButtonState(state) {
    submitBtn.classList.remove('is-sending', 'is-sent', 'is-error');

    if (state === 'sending') {
      submitBtn.disabled = true;
      submitBtn.style.opacity = '0.85';
      btnText.textContent = 'Enviando...';
      submitBtn.classList.add('is-sending');
    } else if (state === 'sent') {
      submitBtn.disabled = false;
      submitBtn.style.opacity = '1';
      btnText.textContent = '¡Mensaje enviado!';
      submitBtn.classList.add('is-sent');

      setTimeout(() => {
        submitBtn.classList.remove('is-sent');
        btnText.textContent = 'Enviar mensaje';
      }, 3000);
    } else if (state === 'error') {
      submitBtn.disabled = false;
      submitBtn.style.opacity = '1';
      btnText.textContent = 'Error, reintentar';
      submitBtn.classList.add('is-error');

      setTimeout(() => {
        submitBtn.classList.remove('is-error');
        btnText.textContent = 'Enviar mensaje';
      }, 3000);
    } else {
      submitBtn.disabled = false;
      submitBtn.style.opacity = '1';
      btnText.textContent = 'Enviar mensaje';
    }
  }

  function showStatus(type, html) {
    formStatus.className = 'form-status ' + type;
    const icon = type === 'success' ? '✅' : '⚠️';
    formStatus.innerHTML = '<span class="ic-status">' + icon + '</span><div>' + html + '</div>';
    formStatus.style.display = 'flex';
    formStatus.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  }
</script>
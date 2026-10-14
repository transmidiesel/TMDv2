<?php
// =========================================================
// encuesta-satisfaccion/index.php
// Formulario público de encuesta de satisfacción.
// Las preguntas se cargan dinámicamente desde:
//   /api/encuesta-preguntas.php
// Las respuestas se envían a:
//   /api/encuesta-guardar.php  (todavía no existe — Paso 5)
// =========================================================
$base      = '../';
$pageTitle = 'Encuesta de Satisfacción — Transmidiesel S.A.S';

require __DIR__ . '/../partials/head.php';
require __DIR__ . '/../partials/navbar.php';
?>

<div class="page-content" id="pageContent">

<div class="bg-mesh"></div>
<div class="grain"></div>
<div class="cursor-glow" id="cursorGlow"></div>

<!-- ================= HERO ================= -->
<section class="page-hero" style="padding-top:170px;padding-bottom:40px;">
  <div class="wrap">
    <div class="eyebrow-line"><span class="dot"></span>Tu opinión nos importa</div>
    <h1>Encuesta de Satisfacción</h1>
    <p>Ayúdanos a mejorar. Cuéntanos cómo fue tu experiencia con Transmidiesel S.A.S. Tus respuestas son anónimas y nos permiten seguir ofreciendo un servicio de calidad.</p>
  </div>
</section>

<!-- ================= FORMULARIO DINÁMICO ================= -->
<section class="tint" style="padding-top:40px;padding-bottom:60px;">
  <div class="wrap">
    <div class="encuesta-container">

      <!-- Estado de carga -->
      <div id="encuestaLoading" class="encuesta-loading">
        <div class="encuesta-spinner"></div>
        <p>Cargando encuesta…</p>
      </div>

      <!-- Estado de error -->
      <div id="encuestaError" class="encuesta-error" style="display:none;">
        <div class="encuesta-error-icon">⚠️</div>
        <h3>No pudimos cargar la encuesta</h3>
        <p id="encuestaErrorMsg">Por favor recarga la página o inténtalo más tarde.</p>
        <button type="button" class="neu-btn primary magnetic" onclick="location.reload()">Recargar</button>
      </div>

      <!-- Formulario (se llena dinámicamente) -->
      <form id="encuestaForm" class="encuesta-form glass" style="display:none;" novalidate>

        <div class="encuesta-header">
          <span class="kicker">Encuesta de Satisfacción</span>
          <h2>Cuéntanos tu experiencia</h2>
          <p class="encuesta-intro">Todos los campos marcados con <span class="form-req">*</span> son obligatorios.</p>
        </div>

        <!-- Aquí se inyectan las preguntas desde JS -->
        <div id="encuestaPreguntas"></div>

        <div class="form-actions encuesta-actions">
          <button type="submit" class="neu-btn primary magnetic send-btn" id="encuestaSubmitBtn">
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
            <span id="encuestaBtnText">Enviar encuesta</span>
          </button>
        </div>

        <div id="encuestaStatus" class="form-status" style="display:none;" role="alert"></div>

      </form>

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
$extraScripts = ['encuesta-satisfaccion/encuesta.js'];
require __DIR__ . '/../partials/scripts.php';
?>
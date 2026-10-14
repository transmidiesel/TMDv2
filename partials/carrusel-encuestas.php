<?php
// =========================================================
// partials/carrusel-encuestas.php
// Sección del carrusel de encuestas de satisfacción
// para insertar en el index principal debajo de "Noticias TMD".
//
// Requiere: $base (ruta relativa a la raíz del proyecto)
// =========================================================

$base = $base ?? './';
?>
<!-- ================= ENCUESTAS DE SATISFACCIÓN ================= -->
<section id="encuestas" class="encuestas-section">
  <div class="wrap">

    <div class="section-head reveal">
      <span class="kicker">Lo que dicen nuestros clientes</span>
      <h2>Opiniones reales, resultados reales</h2>
      <p>¡Queremos escucharte! Estamos trabajando constantemente para darte un mejor servicio, y tu experiencia es fundamental para lograrlo. ¿Nos regalas un minuto con tu opinión?</p>
    </div>

    <!-- Contenedor del carrusel -->
    <div class="encuestas-marquee-wrap reveal">
      <div class="encuestas-marquee" id="encuestasMarquee">
        <!-- Se llena dinámicamente desde encuestas.js -->
        <div class="encuestas-loading">
          <div class="encuestas-spinner"></div>
          <p>Cargando opiniones…</p>
        </div>
      </div>
    </div>

    <!-- CTA para calificar -->
    <div class="encuestas-cta reveal">
      <div class="encuestas-cta-inner glass">
        <div class="encuestas-cta-text">
          <h3>¡Queremos escucharte!</h3>
          <p>Cuéntanos tu experiencia y ayúdanos a seguir mejorando. Solo toma 2 minutos.</p>
        </div>
        <a href="<?= $base ?>encuesta-satisfaccion/" class="neu-btn primary magnetic">
          Califica tu experiencia
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="width:18px;height:18px;">
            <path d="M5 12h14M13 6l6 6-6 6"/>
          </svg>
        </a>
      </div>
    </div>

  </div>

  <!-- Modal para ver el comentario completo -->
  <div class="encuesta-modal-overlay" id="encuestaModal" role="dialog" aria-modal="true" aria-hidden="true">
    <div class="encuesta-modal-backdrop" data-encuesta-close></div>
    <div class="encuesta-modal">
      <button type="button" class="encuesta-modal-close" data-encuesta-close aria-label="Cerrar">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
          <path d="M18 6 6 18M6 6l12 12"/>
        </svg>
      </button>

      <div class="encuesta-modal-header">
        <div class="encuesta-modal-avatar" id="encuestaModalAvatar">A</div>
        <div class="encuesta-modal-author">
          <h3 id="encuestaModalNombre">Cliente</h3>
          <span id="encuestaModalFecha" class="encuesta-modal-fecha"></span>
        </div>
      </div>

      <div class="encuesta-modal-rating" id="encuestaModalRating"></div>

      <div class="encuesta-modal-comment">
        <p id="encuestaModalComentario"></p>
      </div>
    </div>
  </div>

</section>
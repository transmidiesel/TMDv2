<?php
// =========================================================
// servicios/index.php
// =========================================================
$base      = '../';
$pageTitle = 'Servicios — Transmidiesel S.A.S';

require __DIR__ . '/../partials/head.php';
require __DIR__ . '/../partials/navbar.php';
?>

<div class="page-content" id="pageContent">

<div class="bg-mesh"></div>
<div class="grain"></div>
<div class="cursor-glow" id="cursorGlow"></div>

<!-- ================= HERO ================= -->
<section class="page-hero" style="padding-top:170px;">
  <div class="wrap">
    <div class="eyebrow-line"><span class="dot"></span>Lo que hacemos</div>
    <h1>Servicios Transmidiesel</h1>
    <p>¡Descubre nuestro extenso catálogo de servicios! Te invitamos a explorar una gama variada de opciones diseñadas para satisfacer tus necesidades. Desde soluciones creativas hasta servicios especializados, estamos aquí para ofrecerte lo mejor.</p>

    <div class="quicknav">
      <a href="#mantenimientos">Mantenimientos</a>
      <a href="#suministros">Suministros e Instalación</a>
      <a href="#repotenciacion">Repotenciación</a>
      <a href="#banco-prueba">Banco de Prueba</a>
      <a href="#soporte">Soporte Técnico</a>
    </div>
  </div>
</section>

<!-- ================= ICONOS RESUMEN (5 servicios) ================= -->
<section class="tint">
  <div class="wrap">
    <div class="services-chip-grid">
      <div class="chip glass reveal">
        <div class="chip-icon-slot">
          <img src="imagenes/mantenimiento.gif" alt="Mantenimientos" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
          <span class="mark fallback" style="display:none;">⚙</span>
        </div>
        <div class="name">Mantenimientos</div>
        <div class="tag">Predictivos y Correctivos</div>
      </div>
      <div class="chip glass reveal">
        <div class="chip-icon-slot">
          <img src="imagenes/ico-suministros.gif" alt="Suministros e Instalación" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
          <span class="mark fallback" style="display:none;">📦</span>
        </div>
        <div class="name">Suministros e Instalación</div>
        <div class="tag">Equipos de última tecnología</div>
      </div>
      <div class="chip glass reveal">
        <div class="chip-icon-slot">
          <img src="imagenes/repotenciar.png" alt="Repotenciación" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
          <span class="mark fallback" style="display:none;">🔧</span>
        </div>
        <div class="name">Repotenciación</div>
        <div class="tag">Actualización de equipos</div>
      </div>
      <div class="chip glass reveal">
        <div class="chip-icon-slot">
          <img src="imagenes/banco-pruebas.png" alt="Banco de Prueba" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
          <span class="mark fallback" style="display:none;">🧪</span>
        </div>
        <div class="name">Banco de Prueba</div>
        <div class="tag">Hasta 1000 HP</div>
      </div>
      <div class="chip glass reveal">
        <div class="chip-icon-slot">
          <img src="imagenes/asesoria.png" alt="Asesoría Especializada" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
          <span class="mark fallback" style="display:none;">🎧</span>
        </div>
        <div class="name">Asesoría Especializada</div>
        <div class="tag">Soporte técnico</div>
      </div>
    </div>
  </div>
</section>

<!-- ================= SERVICIO 1 — MANTENIMIENTOS ================= -->
<section id="mantenimientos">
  <div class="wrap">
    <div class="service-row">
      <div class="service-media reveal">
        <img src="imagenes/mantenimientos.JPG" alt="Mantenimientos Transmidiesel">
      </div>
      <div class="service-text reveal">
        <span class="kicker">Servicio 01</span>
        <h2>Mantenimientos</h2>
        <p class="service-lead"><strong>Predictivos, Preventivos y Correctivos</strong></p>
        <p>Transmidiesel S.A.S. posee amplia experiencia en el mantenimiento y reparación general de transmisiones marinas. Para la prestación de este servicio contamos con las siguientes rutinas:</p>
        <ul class="service-list">
          <li>Mantenimiento predictivo basado en análisis y monitoreo de condición.</li>
          <li>Mantenimiento preventivo programado según horas de operación.</li>
          <li>Mantenimiento correctivo con repuestos originales.</li>
          <li>Diagnóstico especializado de transmisiones marinas.</li>
        </ul>
        <a href="../contactenos/" class="neu-btn primary magnetic">Conoce más</a>
      </div>
    </div>
  </div>
</section>

<!-- ================= SERVICIO 2 — SUMINISTROS E INSTALACIÓN ================= -->
<section id="suministros" class="tint">
  <div class="wrap">
    <div class="service-row reverse">
      <div class="service-media reveal">
        <img src="imagenes/mantenimientos.JPG" alt="Suministros e Instalación">
      </div>
      <div class="service-text reveal">
        <span class="kicker">Servicio 02</span>
        <h2>Suministros e Instalación</h2>
        <p>Transmidiesel cuenta con el respaldo de fábricas reconocidas mundialmente para suministrar a sus clientes equipos de última tecnología, que satisfagan sus necesidades de producción o funcionamiento.</p>
        <p>Igualmente, cuenta con el personal técnico capacitado para la instalación y mantenimiento de los equipos.</p>
        <ul class="service-list">
          <li>Suministro de equipos con respaldo de fábrica internacional.</li>
          <li>Instalación por personal técnico certificado.</li>
          <li>Puesta en marcha y capacitación de operación.</li>
        </ul>
        <a href="../contactenos/" class="neu-btn primary magnetic">Conoce más</a>
      </div>
    </div>
  </div>
</section>

<!-- ================= SERVICIO 3 — REPOTENCIACIÓN (VIDEO SCRUB STICKY) ================= -->
<section id="repotenciacion" class="service-scrub-section">
  <div class="service-scrub-pin">
    <div class="wrap">
      <div class="service-row">
        <div class="service-media service-media-scrub" id="repotenciacionMedia">
          <video
            id="repotenciacionVideo"
            class="service-scrub-video"
            src="imagenes/repotenciacion.mp4"
            muted
            playsinline
            webkit-playsinline="true"
            preload="auto"></video>

          <div class="service-scrub-final" id="repotenciacionFinal" aria-hidden="true">
            <img src="imagenes/repotenciacion.png" alt="Repotenciación de Equipos" loading="lazy">
          </div>

          <div class="service-scrub-progress" aria-hidden="true">
            <span id="repotenciacionProgress"></span>
          </div>
        </div>

        <div class="service-text">
          <span class="kicker">Servicio 03</span>
          <h2>Repotenciación de Equipos</h2>
          <p>Es la actualización de los equipos viejos que se encuentran en buen estado mecánico. Los equipos repotenciados aumentan la productividad, minimizan costos y generan mayores utilidades a la empresa.</p>
          <p>Para llevar a cabo el proceso de Repotenciación, el departamento de Ingeniería de Transmidiesel ha establecido un protocolo para evaluar la factibilidad de repotenciar el equipo o de cambiarlo en caso de requerirse.</p>
          <ul class="service-list">
            <li>Evaluación técnica de factibilidad.</li>
            <li>Aumento de productividad sin cambiar el equipo completo.</li>
            <li>Reducción de costos operativos y mayor vida útil.</li>
          </ul>
          <a href="../contactenos/" class="neu-btn primary magnetic">Conoce más</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ================= SERVICIO 4 — BANCO DE PRUEBA ================= -->
<section id="banco-prueba" class="tint">
  <div class="wrap">
    <div class="service-row reverse">
      <div class="service-media reveal">
        <img src="imagenes/banco-prueba.JPG" alt="Banco de Prueba">
      </div>
      <div class="service-text reveal">
        <span class="kicker">Servicio 04</span>
        <h2>Banco de Prueba</h2>
        <p>En Transmidiesel, desde nuestros inicios, vimos la necesidad de realizar pruebas a los equipos y transmisiones que reparábamos, atendiendo los reclamos de garantía que en ocasiones presentaban nuestros clientes y que no correspondían al trabajo entregado.</p>
        <p>Es así, que decidimos diseñar un banco de prueba utilizando un motor diésel para transmitir potencia y movimiento a las transmisiones, con un rango aproximado de 500 HP.</p>
        <p>Posteriormente, debido a la necesidad de incrementar la potencia de prueba, para llegar a transmisiones de 1000 HP, decidimos innovar y reemplazamos el viejo motor diésel por un motor eléctrico y un variador de velocidad. Con este cambio se lograron excelentes resultados, como son la eliminación del ruido y gases, aportando además un avance ecológico durante la prueba y mayor calidad en el trabajo y confiabilidad para nuestros clientes.</p>
        <ul class="service-list">
          <li>Pruebas de transmisiones hasta 1000 HP.</li>
          <li>Motor eléctrico + variador de velocidad (tecnología limpia).</li>
          <li>Sin ruido ni emisión de gases.</li>
          <li>Mayor confiabilidad y respaldo para el cliente.</li>
        </ul>
        <a href="../contactenos/" class="neu-btn primary magnetic">Conoce más</a>
      </div>
    </div>
  </div>
</section>

<!-- ================= SERVICIO 5 — SOPORTE TÉCNICO ================= -->
<section id="soporte">
  <div class="wrap">
    <div class="service-row">
      <div class="service-media reveal">
        <img src="imagenes/soporte-tecnico.JPG" alt="Soporte Técnico y Asesoría">
      </div>
      <div class="service-text reveal">
        <span class="kicker">Servicio 05</span>
        <h2>Soporte Técnico y Asesoría Especializada</h2>
        <p>Contamos con un equipo de ingenieros y técnicos altamente calificados para brindarte asesoría especializada en la selección, operación y mantenimiento de tus equipos.</p>
        <p>Entra al siguiente enlace y pregunta por nuestros servicios de asesoría especializada.</p>
        <ul class="service-list">
          <li>Técnicos certificados con experiencia comprobada.</li>
          <li>Repuestos 100% originales y garantizados.</li>
          <li>Banco de pruebas propio en sede.</li>
          <li>Diagnóstico avanzado y reparación precisa.</li>
          <li>Atención personalizada y respuesta rápida.</li>
        </ul>
        <a href="https://serviciotecnicotmd.com/" target="_blank" rel="noopener" class="neu-btn primary magnetic">Ir a Servicio Técnico</a>
      </div>
    </div>
  </div>
</section>

<!-- ================= CTA ================= -->
<section id="contacto" class="tint">
  <div class="wrap">
    <div class="cta-band glass reveal">
      <h2>¿Necesitas un servicio específico?</h2>
      <p>Escríbenos y con gusto te asesoramos. Contamos con el equipo técnico y la experiencia para resolver tus necesidades.</p>
      <a href="../contactenos/" class="neu-btn primary magnetic">Ir a Contáctenos</a>
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
$extraScripts = ['servicios/servicios.js'];
require __DIR__ . '/../partials/scripts.php';
?>
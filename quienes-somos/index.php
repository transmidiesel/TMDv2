<?php
// =========================================================
// quienes-somos/index.php
// =========================================================
$base      = '../';
$pageTitle = 'Quiénes Somos — Transmidiesel S.A.S';

// Lee misión y visión desde la DB
require_once __DIR__ . '/../includes/db.php';

$config = [];
try {
    $stmt = $pdo->query("SELECT clave, valor FROM configuracion");
    while ($row = $stmt->fetch()) {
        $config[$row['clave']] = $row['valor'];
    }
} catch (Exception $e) {
    $config = [];
}

$mision = $config['mision_texto'] ?? 'Suministrar maquinaria, equipos, partes y soluciones integrales de ingeniería en los sectores naval, agrícola, minero, petrolero, transportador e industrial, buscando la satisfacción de las necesidades y expectativas de nuestros clientes, mediante talento humano competente en pro del mejoramiento continuo en la prestación del servicio, en sinergia con las partes interesadas.';
$vision = $config['vision_texto'] ?? 'Posicionarnos para el año 2026, en el mercado colombiano como empresa líder, reconocida por la calidad en soluciones integrales de ingeniería, en los sectores naval, agrícola, minero, petrolero, transportador e industrial, con talento humano competente en la prestación de servicios, buscando la satisfacción de las necesidades y expectativas de nuestros clientes.';

require __DIR__ . '/../partials/head.php';
require __DIR__ . '/../partials/navbar.php';
?>

<div class="page-content" id="pageContent">

<div class="bg-mesh"></div>
<div class="grain"></div>
<div class="cursor-glow" id="cursorGlow"></div>

<!-- ================= HERO CARRUSEL DE SECTORES ================= -->
<section class="sector-hero" id="quienes">
  <div class="sector-carousel" id="sectorCarousel">
    <div class="sector-slide active" data-sector="Industrial">
      <img src="imagenes/sector-industrial.webp" alt="Sector Industrial" onerror="this.style.display='none';">
      <div class="sector-fallback"></div>
    </div>
    <div class="sector-slide" data-sector="Naval">
      <img src="imagenes/sector-naval.webp" alt="Sector Naval" onerror="this.style.display='none';">
      <div class="sector-fallback"></div>
    </div>
    <div class="sector-slide" data-sector="Agrícola">
      <img src="imagenes/sector-agricola.webp" alt="Sector Agrícola" onerror="this.style.display='none';">
      <div class="sector-fallback"></div>
    </div>
    <div class="sector-slide" data-sector="Minero">
      <img src="imagenes/sector-minero.webp" alt="Sector Minero" onerror="this.style.display='none';">
      <div class="sector-fallback"></div>
    </div>
    <div class="sector-slide" data-sector="Petrolero">
      <img src="imagenes/sector-petrolero.webp" alt="Sector Petrolero" onerror="this.style.display='none';">
      <div class="sector-fallback"></div>
    </div>

    <div class="sector-overlay"></div>
    <div class="sector-content wrap">
      <div class="sector-logo">
        <img src="../logos/Logo.webp" alt="Transmidiesel" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
        <div class="logo-fallback" style="display:none;">LOGO<br>.WEBP</div>
      </div>
      <span class="sector-kicker">Respaldo en el sector:</span>
      <h1 class="sector-name" id="sectorName">Industrial</h1>
      <div class="sector-socials">
        <a href="https://www.facebook.com/Transmidiesel" target="_blank" rel="noopener" aria-label="Facebook">
          <svg viewBox="0 0 24 24" fill="currentColor"><path d="M13.5 22v-8h2.7l.4-3.1h-3.1V8.9c0-.9.3-1.5 1.6-1.5h1.7V4.6c-.3 0-1.3-.1-2.4-.1-2.4 0-4 1.5-4 4.1v2.3H7.6V14h2.8v8h3.1z"/></svg>
        </a>
        <a href="https://www.instagram.com/transmidiesel/" target="_blank" rel="noopener" aria-label="Instagram">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
        </a>
        <a href="https://www.tiktok.com/@transmidiesel" target="_blank" rel="noopener" aria-label="TikTok">
          <svg viewBox="0 0 24 24" fill="currentColor"><path d="M16.5 3c.3 1.9 1.4 3.2 3.3 3.5v2.4c-1.2.1-2.3-.2-3.3-.9v6.4c0 3.3-2.6 5.6-5.6 5.6S5.3 17.7 5.3 14.6c0-2.9 2.2-5.3 5.2-5.3.3 0 .5 0 .8.1v2.5c-.3-.1-.5-.1-.8-.1-1.5 0-2.7 1.2-2.7 2.8 0 1.6 1.2 2.7 2.7 2.7 1.5 0 2.7-1.1 2.7-2.7V3h3.3z"/></svg>
        </a>
        <a href="https://www.linkedin.com/company/transmidiesel/" target="_blank" rel="noopener" aria-label="LinkedIn">
          <svg viewBox="0 0 24 24" fill="currentColor"><path d="M4.98 3.5A2.5 2.5 0 1 0 5 8.5a2.5 2.5 0 0 0-.02-5zM3 9.5h4V21H3V9.5zm6.5 0h3.8v1.6h.05c.53-1 1.83-2.05 3.77-2.05 4.03 0 4.78 2.65 4.78 6.1V21h-4v-5.2c0-1.24-.02-2.84-1.73-2.84-1.73 0-2 1.35-2 2.75V21h-4V9.5z"/></svg>
        </a>
        <span class="sector-handle">transmidiesel</span>
      </div>
    </div>
    <div class="sector-dots" id="sectorDots">
      <span class="sector-dot active"></span>
      <span class="sector-dot"></span>
      <span class="sector-dot"></span>
      <span class="sector-dot"></span>
      <span class="sector-dot"></span>
    </div>
  </div>
</section>

<!-- ================= QUIÉNES SOMOS — TEXTO ================= -->
<section class="tint">
  <div class="wrap">
    <div class="section-head reveal">
      <h2>Quiénes Somos</h2>
    </div>
    <div class="prose-card glass reveal">
      <p>
        Transmidiesel S.A.S, líder en importación y comercialización de equipos industriales y servicios de mantenimiento, respalda diversos sectores con marcas reconocidas globalmente. Nuestro equipo altamente calificado y nuestras instalaciones garantizan soluciones integrales, respaldadas por un amplio stock de productos. Confía en nuestra experiencia para satisfacer tus necesidades.
      </p>
    </div>
  </div>
</section>

<!-- ================= LICENCIA DIMAR ================= -->
<section id="dimar">
  <div class="wrap">
    <div class="section-head reveal">
      <span class="kicker">Respaldo legal</span>
      <h2>Licencia DIMAR</h2>
      <p>Transmidiesel cuenta licencia otorgadas por la Dirección General Marítima de la República de Colombia – DIMAR:</p>
    </div>
    <div class="license-card glass reveal">
      <div class="license-badge">DIMAR</div>
      <div>
        <h3>Licencia de Explotación Comercial como Proveedor de Suministros y Servicios al Sector Marítimo</h3>
        <p style="color:var(--metal-light);font-size:15px;line-height:1.7;margin:0;">fabricación, reparación y mantenimiento, alquiler, venta de equipos y repuestos en el territorio colombiano.</p>
      </div>
    </div>
  </div>
</section>

<!-- ================= MISIÓN Y VISIÓN ================= -->
<section id="mision">
  <div class="wrap">
    <div class="section-head reveal">
      <span class="kicker">Hacia dónde vamos</span>
      <h2>Misión y Visión</h2>
    </div>
    <div class="mv-grid">
      <div class="mv-card glass reveal">
        <span class="mv-kicker">Misión</span>
        <p><?= htmlspecialchars($mision) ?></p>
      </div>
      <div class="mv-card glass reveal" id="vision">
        <span class="mv-kicker">Visión</span>
        <p><?= htmlspecialchars($vision) ?></p>
      </div>
    </div>
  </div>
</section>

<!-- ================= VALORES CORPORATIVOS ================= -->
<section id="valores" class="tint">
  <div class="wrap">
    <div class="section-head reveal">
      <span class="kicker">Lo que nos guía</span>
      <h2>Valores Corporativos</h2>
    </div>
    <div class="values-intro reveal">
      <p>Mostraremos con orgullo los valores corporativos de nuestra empresa, destacando nuestra integridad, compromiso, y excelencia en cada acción. Desde la transparencia hasta la innovación, cada valor guía nuestra labor diaria para brindar el mejor servicio a nuestros clientes y contribuir positivamente en la comunidad.</p>
    </div>
    <div class="values-grid">
      <div class="chip glass reveal value-chip">
        <div class="value-icon-slot">
          <img src="imagenes/liderazgo.png" alt="Liderazgo">
        </div>
        <div class="name">Liderazgo</div>
        <div class="value-desc">Desarrollamos habilidades para fijar objetivos, su seguimiento y la capacidad de integrar las opiniones de los demás, motivando e inspirando a los colaboradores.</div>
      </div>
      <div class="chip glass reveal value-chip">
        <div class="value-icon-slot">
          <img src="imagenes/responsabilidad.png" alt="Responsabilidad">
        </div>
        <div class="name">Responsabilidad</div>
        <div class="value-desc">Promovemos el cumplimiento de los compromisos adquiridos, generando confianza y tranquilidad en nuestros clientes.</div>
      </div>
      <div class="chip glass reveal value-chip">
        <div class="value-icon-slot">
          <img src="imagenes/equipo.png" alt="Trabajo en equipo">
        </div>
        <div class="name">Trabajo en Equipo</div>
        <div class="value-desc">Fomentamos la colaboración y cooperación entre los integrantes de la organización para lograr el cumplimiento de los objetivos propuestos.</div>
      </div>
    </div>
  </div>
</section>

<!-- ================= SISTEMA DE GESTIÓN INTEGRADO ================= -->
<section id="sgi">
  <div class="wrap">
    <div class="section-head reveal">
      <span class="kicker">Calidad y transparencia</span>
      <h2>Sistema de Gestión Integrado – SGI</h2>
      <p>Encuentra nuestros documentos del SGI en los enlaces inferiores. Reflejamos nuestro compromiso con la excelencia y la transparencia, garantizando que nuestras prácticas cumplan con los más altos estándares en cada aspecto de nuestro trabajo.</p>
    </div>
    <div class="docs-row reveal">
      <a href="https://drive.google.com/file/d/1qbZHbO5VDVK6EZliZGnIBStOkfgvp4iR/view?usp=sharing" target="_blank" rel="noopener" class="doc-link"><span class="doc-ic">📄</span> Política del SGI</a>
      <a href="https://drive.google.com/file/d/12sXK5DLPVNadxPSDLeyWq79RkRol0E_c/view?usp=sharing" target="_blank" rel="noopener" class="doc-link"><span class="doc-ic">📄</span> Política de Tratamiento de Datos</a>
    </div>
  </div>
</section>

<!-- ================= COMPROMISO AMBIENTAL ================= -->
<section id="ambiental" class="tint">
  <div class="wrap">
    <div class="section-head reveal">
      <span class="kicker">Sostenibilidad</span>
      <h2>Compromiso Ambiental</h2>
    </div>
    <div class="prose-card glass reveal">
      <p>Comprometiéndonos con el medio ambiente hemos reducido el uso de papel al digitalizar nuestros procesos. Así vamos en la digitalización hacia una operación más sostenible, promoviendo la eficiencia y la conservación de recursos naturales.</p>
    </div>
  </div>
</section>

<!-- ================= INDICADORES DEL SGI ================= -->
<section id="indicadores-sgi">
  <div class="wrap">
    <div class="section-head reveal">
      <span class="kicker">Medición y mejora continua</span>
      <h2>Indicadores del SGI</h2>
    </div>
    <div class="progress-list">
      <div class="progress-row glass reveal" style="--pct:82%;">
        <div class="progress-top"><span class="progress-label">Gestión Gerencial</span><span class="progress-pct">82%</span></div>
        <div class="progress-track"><div class="progress-fill"></div></div>
      </div>
      <div class="progress-row glass reveal" style="--pct:60%;">
        <div class="progress-top"><span class="progress-label">Gestión Integrada</span><span class="progress-pct">60%</span></div>
        <div class="progress-track"><div class="progress-fill"></div></div>
      </div>
      <div class="progress-row glass reveal" style="--pct:40%;">
        <div class="progress-top"><span class="progress-label">Compras y Comercio Exterior</span><span class="progress-pct">40%</span></div>
        <div class="progress-track"><div class="progress-fill"></div></div>
      </div>
      <div class="progress-row glass reveal" style="--pct:55%;">
        <div class="progress-top"><span class="progress-label">Comercial y Ventas</span><span class="progress-pct">55%</span></div>
        <div class="progress-track"><div class="progress-fill"></div></div>
      </div>
      <div class="progress-row glass reveal" style="--pct:43%;">
        <div class="progress-top"><span class="progress-label">Servicio Técnico</span><span class="progress-pct">43%</span></div>
        <div class="progress-track"><div class="progress-fill"></div></div>
      </div>
    </div>
  </div>
</section>

<!-- ================= SEDES ================= -->
<section id="sedes" class="tint">
  <div class="wrap">
    <div class="section-head reveal">
      <span class="kicker">Cobertura nacional</span>
      <h2>Nuestras sedes</h2>
    </div>
    <div class="sede-grid">

      <div class="sede-card glass reveal">
        <div class="sede-info">
          <span class="sede-kicker">Sede Principal — Cali</span>
          <h3>Transmidiesel S.A.S</h3>
          <div class="sede-line"><span class="ic">📍</span> Carrera 1 # 50N-89, Barrio Evaristo García</div>
          <div class="sede-line"><span class="ic">☎</span> Línea Nacional: +57 316 877 5212</div>
        </div>
        <div class="sede-map">
          <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3982.480851922681!2d-76.50763982416072!3d3.475367796499033!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e30a62cfff209bb%3A0xbefb05c4becedb21!2sTransmidiesel%20Cali!5e0!3m2!1ses!2sco!4v1789768178789!5m2!1ses!2sco" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
        </div>
      </div>

      <div class="sede-card glass reveal">
        <div class="sede-info">
          <span class="sede-kicker">Agencia Caribe — Cartagena</span>
          <h3>Transmidiesel S.A.S</h3>
          <div class="sede-line"><span class="ic">📍</span> Diagonal 22 # 37 – 54, Av Crisanto Luque — Barrio El Bosque</div>
          <div class="sede-line"><span class="ic">☎</span> Línea Nacional: +57 316 877 5212</div>
        </div>
        <div class="sede-map">
          <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d245.26278235061307!2d-75.52076325154513!3d10.405325076502088!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8ef6258ebc8a871b%3A0xaf5182d52c8b5cd9!2sTransmidiesel%20Cartagena!5e0!3m2!1ses!2sco!4v1790352927681!5m2!1ses!2sco" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
        </div>
      </div>

    </div>

    <div class="contact-grid">
      <div class="contact-card glass reveal">
        <div class="role">Administración · Cali</div>
        <div class="person">Diana Marcela Ordoñez</div>
        <div class="phone">+57 315 573 5099</div>
      </div>
      <div class="contact-card glass reveal">
        <div class="role">Comercial · Cali</div>
        <div class="person">Oscar Asesor Comercial</div>
        <div class="phone">+57 316 522 0137</div>
      </div>
      <div class="contact-card glass reveal">
        <div class="role">Administración · Cartagena</div>
        <div class="person">Manuel Blanco</div>
        <div class="phone">+57 316 877 5207</div>
      </div>
      <div class="contact-card glass reveal">
        <div class="role">Comercial · Cartagena</div>
        <div class="person">Hebert Caraballo</div>
        <div class="phone">+57 316 877 5201</div>
      </div>
    </div>
  </div>
</section>

<!-- ================= CTA BAND ================= -->
<section id="contacto">
  <div class="wrap">
    <div class="cta-band glass reveal">
      <h2>Es un placer asesorarte, ¿cómo podemos ayudarte?</h2>
      <p>Hola! Somos Transmidiesel S.A.S. — TMD Línea Nacional. Escríbenos y con gusto te atendemos.</p>
      <a href="tel:+573168775212" class="neu-btn primary magnetic">Llamar Línea Nacional</a>
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
<?php
// =========================================================
// index.php — Página de inicio
// =========================================================
$base      = './';
$pageTitle = 'Transmidiesel S.A.S — Soluciones Industriales Diésel';

require __DIR__ . '/partials/head.php';
require __DIR__ . '/partials/navbar.php';
?>

<div class="page-content" id="pageContent">

<div class="bg-mesh"></div>
<div class="grain"></div>
<div class="cursor-glow" id="cursorGlow"></div>

<!-- ================= FX: CAPA DE AGUA + MECÁNICA ================= -->
<canvas id="water-canvas" aria-hidden="true"></canvas>

<div class="mech-overlay" aria-hidden="true"></div>

<!-- ================= HERO CON VIDEO CONTROLADO POR SCROLL ================= -->
<section class="hero-scroll" id="inicio">
  <div class="hero-pin">
    <video id="heroVideo" class="hero-video" src="barco.mp4" muted playsinline webkit-playsinline="true" preload="auto"></video>
    <div class="hero-video-overlay"></div>

    <div class="wrap hero-content">

      <div class="hero-layer hero-layer-a">
        <div class="eyebrow-line"><span class="dot"></span>Ingeniería diésel para el sector industrial</div>
        <h1 class="hero-title">
          <span class="reveal-line"><span class="reveal-inner">Potencia, precisión</span></span>
          <span class="reveal-line"><span class="reveal-inner">y respaldo técnico.</span></span>
        </h1>
        <p class="hero-sub">
          Transmidiesel S.A.S, líder en importación y comercialización de equipos industriales y servicios de mantenimiento, respalda diversos sectores con marcas reconocidas globalmente. Nuestro equipo altamente calificado y nuestras instalaciones garantizan soluciones integrales, respaldadas por un amplio stock de productos. Confía en nuestra experiencia para satisfacer tus necesidades.
        </p>
        <div class="hero-ctas">
          <a href="contactenos/" class="neu-btn primary magnetic">Solicitar asesoría</a>
          <a href="#marcas" class="neu-btn magnetic">Ver catálogo de marcas</a>
        </div>
      </div>

      <div class="hero-layer hero-layer-b">
        <span class="hero-sector-kicker">Sectores que respaldamos</span>
        <h2 class="hero-sector-title">Impulsamos el desarrollo de diferentes sectores</h2>
        <p class="hero-sector-sub">
          Con soluciones especializadas y el respaldo de un equipo comprometido con la eficiencia y el rendimiento.
        </p>
      </div>

    </div>

    <div class="hero-sectors-track" id="heroSectorsTrack">
      <figure class="hero-sector-img" data-side="left">
        <img src="img-hero/Naval-hero.jpg" alt="Sector Naval" loading="lazy" decoding="async">
        <figcaption class="hero-sector-caption">
          <span class="hero-sector-caption-kicker">Sector</span>
          <span class="hero-sector-caption-name">Naval</span>
        </figcaption>
      </figure>
      <figure class="hero-sector-img" data-side="right">
        <img src="img-hero/Agrícola-hero.jpg" alt="Sector Agrícola" loading="lazy" decoding="async">
        <figcaption class="hero-sector-caption">
          <span class="hero-sector-caption-kicker">Sector</span>
          <span class="hero-sector-caption-name">Agrícola</span>
        </figcaption>
      </figure>
      <figure class="hero-sector-img" data-side="left">
        <img src="img-hero/Petrolero-hero.jpg" alt="Sector Petrolero" loading="lazy" decoding="async">
        <figcaption class="hero-sector-caption">
          <span class="hero-sector-caption-kicker">Sector</span>
          <span class="hero-sector-caption-name">Petrolero</span>
        </figcaption>
      </figure>
      <figure class="hero-sector-img" data-side="right">
        <img src="img-hero/Minero-hero.jpg" alt="Sector Minero" loading="lazy" decoding="async">
        <figcaption class="hero-sector-caption">
          <span class="hero-sector-caption-kicker">Sector</span>
          <span class="hero-sector-caption-name">Minero</span>
        </figcaption>
      </figure>
      <figure class="hero-sector-img" data-side="left">
        <img src="img-hero/Industrial-hero.jpg" alt="Sector Industrial" loading="lazy" decoding="async">
        <figcaption class="hero-sector-caption">
          <span class="hero-sector-caption-kicker">Sector</span>
          <span class="hero-sector-caption-name">Industrial</span>
        </figcaption>
      </figure>
      <figure class="hero-sector-img hero-sector-img--final" data-side="center">
        <img src="img-hero/banner-final.jpg" alt="Transmidiesel" loading="lazy" decoding="async">
      </figure>
    </div>

    <div class="hero-scroll-hint" id="heroScrollHint"><span></span>Desplázate para explorar</div>
  </div>
</section>

<!-- ================= INDICADORES ================= -->
<section id="indicadores">
  <div class="wrap">
    <div class="stats-grid">
      <div class="stat-card glass reveal">
        <div class="stat-num"><span class="plus"></span><span class="count" data-target="32">0</span></div>
        <div class="stat-label">Años de Experiencia</div>
      </div>
      <div class="stat-card glass reveal">
        <div class="stat-num"><span class="plus">+</span><span class="count" data-target="1000">0</span></div>
        <div class="stat-label">Clientes</div>
      </div>
      <div class="stat-card glass reveal">
        <div class="stat-num"><span class="plus">+</span><span class="count" data-target="3500">0</span></div>
        <div class="stat-label">Productos en Stock</div>
      </div>
      <div class="stat-card glass reveal">
        <div class="stat-num"><span class="plus">+</span><span class="count" data-target="5000">0</span></div>
        <div class="stat-label">Servicios Realizados</div>
      </div>
    </div>
  </div>
</section>

<!-- ================= NUESTRAS MARCAS ================= -->
<section id="marcas">
  <div class="wrap">
    <div class="section-head reveal">
      <span class="kicker">Catálogo</span>
      <h2>Nuestras Marcas</h2>
      <p>Toque las marcas para conocer más sobre cada una.</p>
    </div>
    <div class="marcas-grid" id="marcasGrid"></div>
  </div>
</section>

<!-- ================= CERTIFICACIONES ================= -->
<section id="certificaciones">
  <div class="wrap">
    <div class="section-head reveal">
      <span class="kicker">Respaldo</span>
      <h2>Certificaciones y Licencias</h2>
      <p>Toque los logos para validar las certificaciones y licencias.</p>
    </div>
    <div class="cert-grid" id="certGrid"></div>
  </div>
</section>

<!-- ================= NOTICIAS TMD ================= -->
<section id="noticias">
  <div class="wrap">
    <div class="section-head reveal">
      <span class="kicker">Noticias TMD</span>
      <h2>¡No te pierdas ni un detalle de nuestras últimas noticias corporativas!</h2>
    </div>
    <div class="news-scroller" id="newsScroller"></div>
  </div>
</section>

<!-- ================= REDES SOCIALES ================= -->
<section id="instagram">
  <div class="wrap">
    <div class="section-head reveal">
      <span class="kicker">Síguenos</span>
      <h2>Nuestras últimas publicaciones</h2>
      <p>Conéctate con Transmidiesel en todas nuestras redes sociales: novedades, proyectos, eventos y todo lo que hacemos día a día.</p>
    </div>

    <div class="instagram-embed glass reveal">
      <script src="https://elfsightcdn.com/platform.js" async></script>
      <div class="elfsight-app-db956738-2108-423d-a9db-ce5b99009da3" data-elfsight-app-lazy></div>
    </div>

    <div style="text-align:center;margin-top:32px;">
      <a href="https://www.instagram.com/transmidiesel/" target="_blank" rel="noopener" class="neu-btn primary magnetic">
        Seguir @transmidiesel
      </a>
    </div>
  </div>
</section>

<!-- ================= SEDES ================= -->
<section id="sedes">
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

<?php require __DIR__ . '/partials/footer.php'; ?>
<?php require __DIR__ . '/partials/whatsapp.php'; ?>

<!-- ================= FX: CURSOR PERSONALIZADO ================= -->
<div class="cursor-core" aria-hidden="true"></div>
<div class="cursor-ring" aria-hidden="true"></div>

<?php
$extraScripts = ['public.js'];
require __DIR__ . '/partials/scripts.php';
?>

<meta name="google-site-verification" content="vdtGPZ71j7mY6fKsNS_jnPuxztI6mJrZU7pOJK8GWFo" />
</body>
</html>
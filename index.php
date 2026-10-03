<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Transmidiesel S.A.S — Soluciones Industriales Diésel</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="styles.css?v=mega1">
<link rel="icon" type="image/png" sizes="32x32" href="logos/favicon_io/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="logos/favicon_io/favicon-16x16.png">
<link rel="apple-touch-icon" sizes="180x180" href="logos/favicon_io/apple-touch-icon.png">
<link rel="manifest" href="logos/favicon_io/site.webmanifest">
</head>
<body>

<div class="page-transition" id="pageTransition"></div>

<!-- ================= NAVBAR ================= -->
<header class="nav" id="navHeader">
  <div class="nav-shell">
    <div class="wrap nav-inner">
      <div class="logo-wrap">
        <div class="logo-box">
          <img id="navLogoTop" src="logos/Logo2.png" alt="Transmidiesel" class="logo-top" onerror="this.style.display='none';">
          <img id="navLogoScrolled" src="logos/Logo.png" alt="Transmidiesel" class="logo-scrolled" onerror="this.style.display='none';">
          <div class="logo-fallback" style="display:none;">LOGO<br>.PNG</div>
        </div>
      </div>
      <nav class="links">
        <a href=".">Inicio</a>
        <a href="quienes-somos/">Quiénes Somos</a>
        <a href="servicios/">Servicios</a>
        <a href="productos/" class="mega-trigger" id="megaBtn" aria-haspopup="true" aria-expanded="false" aria-controls="megaMenu">Productos <svg class="mega-chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg></a>
        <a href="empleados/">Empleados</a>
      </nav>
      <div class="nav-cta">
        <a href="contactenos/" class="neu-btn ghost magnetic">Contáctanos</a>
        <button class="burger" id="burgerBtn" aria-label="Abrir menú"><span></span></button>
      </div>
    </div>

    <!-- MEGA MENÚ · PRODUCTOS -->
    <div class="mega-menu" id="megaMenu" role="region" aria-label="Menú de productos">
      <div class="mega-grid">
        <div class="mega-feature">
          <span class="mega-eyebrow">Catálogo</span>
          <h4>Equipos industriales y repuestos</h4>
          <p>Importación y comercialización de equipos con respaldo de marcas reconocidas globalmente y amplio stock.</p>
          <a href="productos/" class="mega-feature-btn">Ver todo el catálogo
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
          </a>
        </div>

        <div class="mega-cat">
          <div class="mega-cat-title"><span class="mega-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 20l6-8 4 4 8-11"/><path d="M14 5h6v6"/></svg></span><strong>Por sector</strong></div>
          <a href="productos/#naval">Naval</a>
          <a href="productos/#agricola">Agrícola</a>
          <a href="productos/#petrolero">Petrolero</a>
          <a href="productos/#minero">Minero</a>
          <a href="productos/" class="view-all">Ver todos</a>
        </div>

        <div class="mega-cat">
          <div class="mega-cat-title"><span class="mega-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3 4 7v6c0 4 3.4 7 8 8 4.6-1 8-4 8-8V7l-8-4z"/><path d="m9 12 2 2 4-4"/></svg></span><strong>Marcas</strong></div>
          <a href="productos/#oxe-marine">OXE Marine</a>
          <a href="productos/#duramax">Duramax®</a>
          <a href="#marcas">Todas las marcas</a>
          <a href="productos/" class="view-all">Ver todas</a>
        </div>

        <div class="mega-cat">
          <div class="mega-cat-title"><span class="mega-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14.7 6.3a4 4 0 0 0-5.4 5.4L3 18l3 3 6.3-6.3a4 4 0 0 0 5.4-5.4l-2.6 2.6-2.4-.6-.6-2.4 2.6-2.6z"/></svg></span><strong>Servicios</strong></div>
          <a href="servicios/#reparacion">Reparación y mantenimiento</a>
          <a href="servicios/#alquiler">Alquiler de equipos</a>
          <a href="servicios/#repuestos">Venta de equipos y repuestos</a>
          <a href="servicios/" class="view-all">Ver todos</a>
        </div>

        <div class="mega-special">
          <a href="contactenos/" class="special-card">
            <span class="mega-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 5h16v11H8l-4 4V5z"/></svg></span>
            <span><strong>Cotiza tu equipo</strong><small>Te asesoramos sin compromiso</small></span>
          </a>
          <a href="servicios/" class="special-card">
            <span class="mega-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3M4.9 4.9 7 7M17 17l2.1 2.1M19.1 4.9 17 7M7 17l-2.1 2.1"/></svg></span>
            <span><strong>Servicio técnico</strong><small>Taller y soporte especializado</small></span>
          </a>
        </div>
      </div>
    </div>
  </div>
</header>

<div class="page-content" id="pageContent">

<div class="bg-mesh"></div>
<div class="grain"></div>
<div class="cursor-glow" id="cursorGlow"></div>

<!-- ================= FX: CAPA DE AGUA + MECÁNICA (agregado, no interactivo) ================= -->
<canvas id="water-canvas" aria-hidden="true"></canvas>

<div class="mech-overlay" aria-hidden="true">
  <!-- Las tuercas (tuerca.png) se generan por JS: cantidad, posición, tamaño
       y desenfoque quedan al azar en cada carga. -->
</div>

<!-- ================= HERO CON VIDEO CONTROLADO POR SCROLL ================= -->
<section class="hero-scroll" id="inicio">
  <div class="hero-pin">
    <video id="heroVideo" class="hero-video" src="barco.mp4" muted playsinline webkit-playsinline="true" preload="auto"></video>
    <div class="hero-video-overlay"></div>

    <div class="wrap hero-content">

      <!-- CAPA A: textos iniciales (aparecen al cargar, se desvanecen con scroll) -->
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

      <!-- CAPA B: sectores que respaldamos (aparece al hacer scroll) -->
      <div class="hero-layer hero-layer-b">
        <span class="hero-sector-kicker">Sectores que respaldamos</span>
        <h2 class="hero-sector-title">Impulsamos el desarrollo de diferentes sectores</h2>
        <p class="hero-sector-sub">
          Con soluciones especializadas y el respaldo de un equipo comprometido con la eficiencia y el rendimiento.
        </p>
      </div>

    </div>

    <!-- ===== SECUENCIA DE IMÁGENES DE SECTORES (scroll-driven) =====
         Va DENTRO de .hero-pin (pantalla completa) y FUERA de .hero-layer-b
         para que las imágenes puedan entrar desde fuera de la pantalla. -->
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
    <!-- ===== /SECUENCIA ===== -->

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
    <div class="marcas-grid" id="marcasGrid">
      <!-- Se llena automáticamente desde la base de datos -->
    </div>
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
    <div class="cert-grid" id="certGrid">
      <!-- Se llena automáticamente desde la base de datos -->
    </div>
  </div>
</section>

<!-- ================= NOTICIAS TMD ================= -->
<section id="noticias">
  <div class="wrap">
    <div class="section-head reveal">
      <span class="kicker">Noticias TMD</span>
      <h2>¡No te pierdas ni un detalle de nuestras últimas noticias corporativas!</h2>
    </div>
    <div class="news-scroller" id="newsScroller">
      <!-- Se llena automáticamente desde la base de datos -->
    </div>
  </div>
</section>

<!-- ================= REDES SOCIALES (WIDGET CONSOLIDADO) ================= -->
<section id="instagram">
  <div class="wrap">
    <div class="section-head reveal">
      <span class="kicker">Síguenos</span>
      <h2>Nuestras últimas publicaciones</h2>
      <p>Conéctate con Transmidiesel en todas nuestras redes sociales: novedades, proyectos, eventos y todo lo que hacemos día a día.</p>
    </div>

    <div class="instagram-embed glass reveal">
      <!-- Elfsight Social Feed | Transmidiesel -->
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

    <!-- Tarjetas unificadas: info + mapa por sede -->
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
          <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3982.480851922681!2d-76.50763982416072!3d3.475367796499033!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e30a62cfff209bb%3A0xbefb05c4becedb21!2sTransmidiesel%20Cali!5e0!3m2!1ses!2sco!4v1789768178789!5m2!1ses!2sco" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
        </div>
      </div>

      <!-- Sede Cartagena -->
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

<!-- ================= FOOTER ================= -->
<footer>
  <div class="wrap">

    <div class="footer-grid">
      <div>
        <div class="logo-wrap" style="margin-bottom:16px;">
          <div class="logo-box">
            <img src="logos/Logo.png" alt="Transmidiesel" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div class="logo-fallback" style="display:none;">LOGO<br>.PNG</div>
          </div>
        </div>
        <p style="color:var(--metal-light);font-size:14px;max-width:320px;">
          Líder en importación y comercialización de equipos industriales y servicios de mantenimiento, con respaldo de marcas reconocidas globalmente.
        </p>
      </div>
      <div>
        <h4>Navegación</h4>
        <ul>
          <li><a href=".">Inicio</a></li>
          <li><a href="quienes-somos/">Quiénes Somos</a></li>
          <li><a href="servicios/">Servicios</a></li>
          <li><a href="productos/">Productos</a></li>
          <li><a href="empleados/">Empleados</a></li>
          <li><a href="contactenos/">Contáctenos</a></li>
        </ul>
      </div>
      <div>
        <h4>Sede Cali</h4>
        <ul>
          <li>Carrera 1 # 50N-89</li>
          <li>Barrio Evaristo García</li>
          <li><a href="tel:+573168775212">+57 316 877 5212</a></li>
        </ul>
      </div>
      <div>
        <h4>Agencia Cartagena</h4>
        <ul>
          <li>Diagonal 22 # 37 – 54</li>
          <li>Av Crisanto Luque — Barrio El Bosque</li>
          <li><a href="tel:+573168775207">+57 316 877 5207</a></li>
        </ul>
      </div>
    </div>

    <!-- Fila de certificados organizada en la parte inferior (sin recuadros) -->
    <div class="footer-certs-strip">
      <div class="footer-certs">
        <a href="https://drive.google.com/file/d/1tUCS5XpRbFDOzavGLTw8GLl5oagov1KM/view" target="_blank" rel="noopener" class="footer-cert" aria-label="Certificación Coface" title="Certificado Coface">
          <img src="Certificados/coface.png" alt="Coface">
        </a>
        <a href="https://drive.google.com/file/d/19A8tL2YiLWpuYOBLWedSsei4WFTLczVw/view" target="_blank" rel="noopener" class="footer-cert" aria-label="Certificación PAR Servicios" title="Certificado PAR Servicios">
          <img src="Certificados/par.png" alt="PAR">
        </a>
        <a href="https://drive.google.com/file/d/1pvsPGOfZW7c02YCRyURUdB-tuwCg8caQ/view" target="_blank" rel="noopener" class="footer-cert" aria-label="Licencia DIMAR" title="Licencia DIMAR">
          <img src="Certificados/dimar logo.png" alt="DIMAR">
        </a>
        <a href="https://www.sgs.com/en/certified-clients-and-products/verify-certificate?id=5d58a8f4-052a-4752-a21d-c325ac1fa388" target="_blank" rel="noopener" class="footer-cert" aria-label="Certificación SGS ISO 9001" title="Certificación SGS ISO 9001">
          <img src="Certificados/ISO-9001.png" alt="ISO 9001">
        </a>
        <a href="https://www.sgs.com/en/certified-clients-and-products/verify-certificate?id=2b3816a8-7ab1-468b-b4c7-d334e0a2586e" target="_blank" rel="noopener" class="footer-cert" aria-label="Certificación SGS ISO 14001" title="Certificación SGS ISO 14001">
          <img src="Certificados/ISO-14001.png" alt="ISO 14001">
        </a>
      </div>
    </div>

    <div class="footer-bottom">
      <span>© 2026 Transmidiesel S.A.S. Todos los derechos reservados.</span>
      <div class="social-row" aria-label="Redes sociales Transmidiesel">
        <a href="https://www.facebook.com/Transmidiesel" target="_blank" rel="noopener" aria-label="Facebook" title="Facebook">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M13.5 22v-8h2.7l.4-3.1h-3.1V8.9c0-.9.3-1.5 1.6-1.5h1.7V4.6c-.3 0-1.3-.1-2.4-.1-2.4 0-4 1.5-4 4.1v2.3H7.6V14h2.8v8h3.1z"/></svg>
        </a>
        <a href="https://www.instagram.com/transmidiesel/" target="_blank" rel="noopener" aria-label="Instagram" title="Instagram">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
        </a>
        <a href="https://www.linkedin.com/company/transmidiesel/" target="_blank" rel="noopener" aria-label="LinkedIn" title="LinkedIn">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M4.98 3.5A2.5 2.5 0 1 0 5 8.5a2.5 2.5 0 0 0-.02-5zM3 9.5h4V21H3V9.5zm6.5 0h3.8v1.6h.05c.53-1 1.83-2.05 3.77-2.05 4.03 0 4.78 2.65 4.78 6.1V21h-4v-5.2c0-1.24-.02-2.84-1.73-2.84-1.73 0-2 1.35-2 2.75V21h-4V9.5z"/></svg>
        </a>
        <a href="https://www.tiktok.com/@transmidiesel" target="_blank" rel="noopener" aria-label="TikTok" title="TikTok">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M16.5 3c.3 1.9 1.4 3.2 3.3 3.5v2.4c-1.2.1-2.3-.2-3.3-.9v6.4c0 3.3-2.6 5.6-5.6 5.6S5.3 17.7 5.3 14.6c0-2.9 2.2-5.3 5.2-5.3.3 0 .5 0 .8.1v2.5c-.3-.1-.5-.1-.8-.1-1.5 0-2.7 1.2-2.7 2.8 0 1.6 1.2 2.7 2.7 2.7 1.5 0 2.7-1.1 2.7-2.7V3h3.3z"/></svg>
        </a>
      </div>
    </div>
  </div>
</footer>

</div>
<!-- ↑ cierra .page-content -->

<!-- ================= WHATSAPP MODULE ================= -->
<div class="wa-float" id="waFloat">

  <!-- Panel de conversación -->
  <div class="wa-panel glass" id="waPanel">
    <div class="wa-head">
      <div class="wa-logo">
        <img src="logos/favicon_io/android-chrome-512x512.png" alt="Transmidiesel" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
        <div class="logo-fallback" style="display:none;font-size:8px;">LOGO</div>
      </div>
      <div>
        <div class="wa-title">TMD - Línea Nacional</div>
        <div class="wa-sub">Hola! Somos Transmidiesel S.A.S.</div>
      </div>
    </div>
    <div class="wa-body">
      <div class="wa-bubble"><strong>Hola! somos Transmidiesel S.A.S.</strong><br>Es un placer asesorarte; ¿cómo podemos ayudarte?</div>
      <a href="https://wa.me/573168775212" target="_blank" class="neu-btn primary magnetic" style="width:100%;justify-content:center;">Iniciar conversación</a>
    </div>
  </div>

  <!-- Botón "Volver arriba" -->
  <button class="back-top magnetic" id="backTop" aria-label="Volver arriba" title="Volver arriba">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
      <path d="M12 19V5"/>
      <path d="M5 12l7-7 7 7"/>
    </svg>
  </button>

  <!-- Botón principal de WhatsApp -->
  <button class="wa-fab magnetic" id="waFab" aria-label="Abrir WhatsApp">
    <svg viewBox="0 0 32 32" fill="none"><path d="M16 3C9 3 3.3 8.6 3.3 15.5c0 2.6.8 5 2.1 7L3 29l6.7-2.3c1.9 1 4.1 1.6 6.3 1.6 7 0 12.7-5.6 12.7-12.5S23 3 16 3z" fill="#ffffff"/><path d="M12.1 10.4c-.3-.7-.6-.7-.9-.7h-.7c-.3 0-.7.1-1 .5-.3.4-1.3 1.2-1.3 3s1.3 3.5 1.5 3.7c.2.3 2.5 4 6.2 5.5 3 1.2 3.6 1 4.3.9.7-.1 2.1-.8 2.4-1.6.3-.8.3-1.5.2-1.6-.1-.2-.3-.3-.7-.5s-2.1-1.1-2.4-1.2c-.3-.1-.6-.2-.8.2-.2.4-.9 1.2-1.1 1.4-.2.2-.4.3-.7.1-.4-.2-1.5-.6-2.8-1.8-1-1-1.7-2.1-1.9-2.5-.2-.4 0-.6.2-.8l.5-.6c.1-.2.2-.3.3-.5.1-.2 0-.4 0-.6 0-.2-.7-1.9-1-2.7z" fill="var(--accent)"/></svg>
  </button>

  <!-- Redes sociales que se despliegan al pasar el cursor sobre el botón de WhatsApp -->
  <div class="wa-socials" id="waSocials" aria-label="Redes sociales Transmidiesel">
    <a href="https://www.facebook.com/Transmidiesel" target="_blank" rel="noopener" class="wa-social-btn" aria-label="Facebook" title="Facebook">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M13.5 22v-8h2.7l.4-3.1h-3.1V8.9c0-.9.3-1.5 1.6-1.5h1.7V4.6c-.3 0-1.3-.1-2.4-.1-2.4 0-4 1.5-4 4.1v2.3H7.6V14h2.8v8h3.1z"/></svg>
    </a>
    <a href="https://www.instagram.com/transmidiesel/" target="_blank" rel="noopener" class="wa-social-btn" aria-label="Instagram" title="Instagram">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
    </a>
    <a href="https://www.tiktok.com/@transmidiesel" target="_blank" rel="noopener" class="wa-social-btn" aria-label="TikTok" title="TikTok">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M16.5 3c.3 1.9 1.4 3.2 3.3 3.5v2.4c-1.2.1-2.3-.2-3.3-.9v6.4c0 3.3-2.6 5.6-5.6 5.6S5.3 17.7 5.3 14.6c0-2.9 2.2-5.3 5.2-5.3.3 0 .5 0 .8.1v2.5c-.3-.1-.5-.1-.8-.1-1.5 0-2.7 1.2-2.7 2.8 0 1.6 1.2 2.7 2.7 2.7 1.5 0 2.7-1.1 2.7-2.7V3h3.3z"/></svg>
    </a>
    <a href="https://www.linkedin.com/company/transmidiesel/" target="_blank" rel="noopener" class="wa-social-btn" aria-label="LinkedIn" title="LinkedIn">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M4.98 3.5A2.5 2.5 0 1 0 5 8.5a2.5 2.5 0 0 0-.02-5zM3 9.5h4V21H3V9.5zm6.5 0h3.8v1.6h.05c.53-1 1.83-2.05 3.77-2.05 4.03 0 4.78 2.65 4.78 6.1V21h-4v-5.2c0-1.24-.02-2.84-1.73-2.84-1.73 0-2 1.35-2 2.75V21h-4V9.5z"/></svg>
    </a>
  </div>

</div>

<!-- ================= FX: CURSOR PERSONALIZADO (agregado) ================= -->
<div class="cursor-core" aria-hidden="true"></div>
<div class="cursor-ring" aria-hidden="true"></div>

<script src="script.js?v=5"></script>
<script src="public.js"></script>
<meta name="google-site-verification" content="vdtGPZ71j7mY6fKsNS_jnPuxztI6mJrZU7pOJK8GWFo" />
</body>
</html>
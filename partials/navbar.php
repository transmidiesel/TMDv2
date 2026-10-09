<?php
// =========================================================
// partials/navbar.php
// Navbar común a todas las páginas.
// Los logos se leen desde la tabla `configuracion` (dinámicos).
// Requiere: $base (ruta relativa a la raíz del proyecto)
// =========================================================

require_once __DIR__ . '/../includes/config_loader.php';

$base          = $base ?? './';
$logoPrincipal = cfg('logo_principal', 'logos/Logo2.png');
$logoScrolled  = cfg('logo_scrolled',  'logos/Logo.png');
?>
<!-- ================= NAVBAR ================= -->
<header class="nav" id="navHeader">
  <div class="nav-shell">
    <div class="wrap nav-inner">
      <div class="logo-wrap">
        <div class="logo-box">
          <img id="navLogoTop" src="<?= $base . htmlspecialchars($logoPrincipal) ?>" alt="Transmidiesel" class="logo-top" onerror="this.style.display='none';">
          <img id="navLogoScrolled" src="<?= $base . htmlspecialchars($logoScrolled) ?>" alt="Transmidiesel" class="logo-scrolled" onerror="this.style.display='none';">
          <div class="logo-fallback" style="display:none;">LOGO<br>.PNG</div>
        </div>
      </div>
      <nav class="links">
        <a href="<?= $base ?>">Inicio</a>
        <a href="<?= $base ?>quienes-somos/">Quiénes Somos</a>
        <a href="<?= $base ?>servicios/">Servicios</a>
        <a href="<?= $base ?>productos/" class="mega-trigger" id="megaBtn" aria-haspopup="true" aria-expanded="false" aria-controls="megaMenu">Productos <svg class="mega-chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg></a>
        <a href="<?= $base ?>empleados/">Empleados</a>
      </nav>
      <div class="nav-cta">
        <a href="<?= $base ?>contactenos/" class="neu-btn ghost magnetic">Contáctanos</a>
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
          <a href="<?= $base ?>productos/" class="mega-feature-btn">Ver todo el catálogo
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
          </a>
        </div>

        <div class="mega-cat">
          <div class="mega-cat-title"><span class="mega-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 20l6-8 4 4 8-11"/><path d="M14 5h6v6"/></svg></span><strong>Por sector</strong></div>
          <a href="<?= $base ?>productos/#naval">Naval</a>
          <a href="<?= $base ?>productos/#agricola">Agrícola</a>
          <a href="<?= $base ?>productos/#petrolero">Petrolero</a>
          <a href="<?= $base ?>productos/#minero">Minero</a>
          <a href="<?= $base ?>productos/" class="view-all">Ver todos</a>
        </div>

        <div class="mega-cat">
          <div class="mega-cat-title"><span class="mega-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3 4 7v6c0 4 3.4 7 8 8 4.6-1 8-4 8-8V7l-8-4z"/><path d="m9 12 2 2 4-4"/></svg></span><strong>Marcas</strong></div>
          <a href="<?= $base ?>productos/#oxe-marine">OXE Marine</a>
          <a href="<?= $base ?>productos/#duramax">Duramax®</a>
          <a href="<?= $base ?>#marcas">Todas las marcas</a>
          <a href="<?= $base ?>productos/" class="view-all">Ver todas</a>
        </div>

        <div class="mega-cat">
          <div class="mega-cat-title"><span class="mega-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14.7 6.3a4 4 0 0 0-5.4 5.4L3 18l3 3 6.3-6.3a4 4 0 0 0 5.4-5.4l-2.6 2.6-2.4-.6-.6-2.4 2.6-2.6z"/></svg></span><strong>Servicios</strong></div>
          <a href="<?= $base ?>servicios/#reparacion">Reparación y mantenimiento</a>
          <a href="<?= $base ?>servicios/#alquiler">Alquiler de equipos</a>
          <a href="<?= $base ?>servicios/#repuestos">Venta de equipos y repuestos</a>
          <a href="<?= $base ?>servicios/" class="view-all">Ver todos</a>
        </div>

        <div class="mega-special">
          <a href="<?= $base ?>contactenos/" class="special-card">
            <span class="mega-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 5h16v11H8l-4 4V5z"/></svg></span>
            <span><strong>Cotiza tu equipo</strong><small>Te asesoramos sin compromiso</small></span>
          </a>
          <a href="<?= $base ?>servicios/" class="special-card">
            <span class="mega-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3M4.9 4.9 7 7M17 17l2.1 2.1M19.1 4.9 17 7M7 17l-2.1 2.1"/></svg></span>
            <span><strong>Servicio técnico</strong><small>Taller y soporte especializado</small></span>
          </a>
        </div>
      </div>
    </div>
  </div>
</header>
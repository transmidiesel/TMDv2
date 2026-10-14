<?php
// =========================================================
// partials/navbar.php
// Navbar unificado con autodetección de link activo y flags.
//
// Flags opcionales (declarar ANTES del require):
//   $navActive     → 'inicio' | 'quienes-somos' | 'servicios' | 'productos' | 'empleados' | 'contactenos'
//                    Si no se define, se autodetecta desde la URL.
//   $navSolid      → true/false. Si true, la navbar va con fondo blanco desde el inicio.
//                    Si no se define, es true por defecto en páginas internas.
//   $navShowMega   → true/false. Si false, no muestra el mega-menú de Productos.
//   $navCtaText    → Texto del botón CTA (por defecto "Contáctanos").
//   $navCtaHref    → URL del botón CTA (por defecto contactenos/).
//   $navCtaTel     → Si true, el botón CTA abre el teléfono en vez de navegar.
// =========================================================

require_once __DIR__ . '/../includes/config_loader.php';

$base          = $base ?? './';
$logoPrincipal = cfg('logo_principal', 'logos/Logo2.webp');
$logoScrolled  = cfg('logo_scrolled',  'logos/Logo.webp');

// ---- Autodetección del link activo ----
if (!isset($navActive)) {
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    // Normalizamos: quitamos el prefijo del proyecto si existe
    $uri = preg_replace('#^/transmiweb#', '', $uri);
    $uri = '/' . trim($uri, '/');

    if ($uri === '/' || $uri === '') {
        $navActive = 'inicio';
    } elseif (strpos($uri, '/quienes-somos') === 0) {
        $navActive = 'quienes-somos';
    } elseif (strpos($uri, '/servicios') === 0) {
        $navActive = 'servicios';
    } elseif (strpos($uri, '/productos') === 0 || strpos($uri, '/marcas') === 0) {
        $navActive = 'productos';
    } elseif (strpos($uri, '/empleados') === 0) {
        $navActive = 'empleados';
    } elseif (strpos($uri, '/contactenos') === 0) {
        $navActive = 'contactenos';
    } else {
        $navActive = '';
    }
}

// ---- Defaults de flags ----
$navSolid    = $navSolid    ?? ($navActive !== 'inicio');
$navShowMega = $navShowMega ?? true;
$navCtaText  = $navCtaText  ?? 'Contáctanos';

if (!isset($navCtaTel)) {
    $navCtaTel = ($navActive === 'contactenos');
}
if ($navCtaTel) {
    $telRaw = cfg('telefono_nacional', '+57 316 877 5212');
    $navCtaHref = 'tel:+' . preg_replace('/\D/', '', $telRaw);
    if (!isset($navCtaText)) $navCtaText = 'Línea Nacional';
} else {
    $navCtaHref = $navCtaHref ?? ($base . 'contactenos/');
}

// Clases del header según flags
$headerClass = 'nav';
if ($navSolid) $headerClass .= ' nav-solid';
?>
<!-- ================= NAVBAR ================= -->
<header class="<?= $headerClass ?>" id="navHeader">
  <div class="nav-shell">
    <div class="wrap nav-inner">
      <div class="logo-wrap">
        <a href="<?= $base ?>" class="logo-box">
          <img id="navLogoTop" src="<?= $base . htmlspecialchars($logoPrincipal) ?>" alt="Transmidiesel" class="logo-top" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
          <img id="navLogoScrolled" src="<?= $base . htmlspecialchars($logoScrolled) ?>" alt="Transmidiesel" class="logo-scrolled" onerror="this.style.display='none';">
          <div class="logo-fallback" style="display:none;">LOGO<br>.PNG</div>
        </a>
      </div>
      <nav class="links">
        <a href="<?= $base ?>" class="<?= $navActive === 'inicio' ? 'active' : '' ?>">Inicio</a>
        <a href="<?= $base ?>quienes-somos/" class="<?= $navActive === 'quienes-somos' ? 'active' : '' ?>">Quiénes Somos</a>
        <a href="<?= $base ?>servicios/" class="<?= $navActive === 'servicios' ? 'active' : '' ?>">Servicios</a>
        <?php if ($navShowMega): ?>
          <a href="<?= $base ?>productos/" class="mega-trigger <?= $navActive === 'productos' ? 'active' : '' ?>" id="megaBtn" aria-haspopup="true" aria-expanded="false" aria-controls="megaMenu">Productos <svg class="mega-chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg></a>
        <?php else: ?>
          <a href="<?= $base ?>productos/" class="<?= $navActive === 'productos' ? 'active' : '' ?>">Productos</a>
        <?php endif; ?>
        <a href="<?= $base ?>empleados/" class="<?= $navActive === 'empleados' ? 'active' : '' ?>">Empleados</a>
      </nav>
      <div class="nav-cta">
        <a href="<?= htmlspecialchars($navCtaHref) ?>" class="neu-btn ghost magnetic"><?= htmlspecialchars($navCtaText) ?></a>
        <button class="burger" id="burgerBtn" aria-label="Abrir menú"><span></span></button>
      </div>
    </div>

    <?php if ($navShowMega): ?>
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
          <a href="https://oxecolombia.com/">OXE Marine</a>
          <a href="<?= $base ?>marcas/duramax/">Duramax®</a>
          <a href="<?= $base ?>#marcas">Todas las marcas</a>
          <a href="<?= $base ?>productos/" class="view-all">Ver todas</a>
        </div>

        <div class="mega-cat">
          <div class="mega-cat-title"><span class="mega-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14.7 6.3a4 4 0 0 0-5.4 5.4L3 18l3 3 6.3-6.3a4 4 0 0 0 5.4-5.4l-2.6 2.6-2.4-.6-.6-2.4 2.6-2.6z"/></svg></span><strong>Servicios</strong></div>
          <a href="<?= $base ?>servicios/#mantenimientos">Reparación y mantenimiento</a>
          <a href="<?= $base ?>servicios/#suministros">Alquiler de equipos</a>
          <a href="<?= $base ?>servicios/#repotenciacion">Venta de equipos y repuestos</a>
          <a href="<?= $base ?>servicios/" class="view-all">Ver todos</a>
        </div>

        <div class="mega-special">
          <a href="<?= $base ?>contactenos/" class="special-card">
            <span class="mega-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 5h16v11H8l-4 4V5z"/></svg></span>
            <span><strong>Cotiza tu equipo</strong><small>Te asesoramos sin compromiso</small></span>
          </a>
          <a href="https://serviciotecnicotmd.com/" target="_blank" rel="noopener" class="special-card">
            <span class="mega-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3M4.9 4.9 7 7M17 17l2.1 2.1M19.1 4.9 17 7M7 17l-2.1 2.1"/></svg></span>
            <span><strong>Servicio técnico</strong><small>Taller y soporte especializado</small></span>
          </a>
        </div>
      </div>
    </div>
    <?php endif; ?>
  </div>
</header>
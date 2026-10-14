<?php
// =========================================================
// productos/index.php
// =========================================================
$base      = '../';
$pageTitle = 'Productos — Transmidiesel S.A.S';

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
    <span class="kicker">Lo que hacemos</span>
    <h1>Nuestros Productos</h1>
    <p>Equipos, repuestos y componentes para los sectores industrial, naval, agrícola, minero y petrolero. Esta página se ampliará próximamente con el catálogo completo.</p>
  </div>
</section>

<!-- ================= LISTADO DE PRODUCTOS ================= -->
<section class="tint">
  <div class="wrap">
    <div class="section-head reveal">
      <span class="kicker">Catálogo</span>
      <h2>Productos Destacados</h2>
      <p>Equipos y repuestos de las mejores marcas para los sectores industrial, naval, agrícola, minero y petrolero.</p>
    </div>

    <div class="productos-grid" id="productosGrid">
      <!-- Se llena dinámicamente desde la API -->
    </div>
  </div>
</section>

<!-- ================= CTA ================= -->
<section id="contacto">
  <div class="wrap">
    <div class="cta-band glass reveal">
      <h2>¿Necesitas un servicio específico?</h2>
      <p>Escríbenos y con gusto te asesoramos.</p>
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
$extraScripts = ['productos/productos.js'];
require __DIR__ . '/../partials/scripts.php';
?>
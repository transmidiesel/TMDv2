<?php
// =========================================================
// TEST DE MIGRACIÓN — BORRAR CUANDO TERMINE LA MIGRACIÓN
// =========================================================

$base      = '../';
$pageTitle = 'TEST — Migración de partials';

require __DIR__ . '/../partials/head.php';
require __DIR__ . '/../partials/navbar.php';
?>

<div class="page-content" id="pageContent">

  <div class="bg-mesh"></div>
  <div class="grain"></div>
  <div class="cursor-glow" id="cursorGlow"></div>

  <section class="page-hero" style="padding-top:170px;">
    <div class="wrap">
      <div class="eyebrow-line"><span class="dot"></span>Test de migración</div>
      <h1>Si ves este texto y el navbar funciona, ¡éxito! ✅</h1>
      <p>Este archivo carga el navbar, footer y WhatsApp desde <code>partials/</code>. Si funciona aquí, funcionará en todas las páginas reales.</p>

      <div class="quicknav" style="margin-top:32px;">
        <a href="../">Volver al inicio</a>
        <a href="../servicios/">Ir a Servicios (aún sin migrar)</a>
        <a href="../quienes-somos/">Ir a Quiénes Somos</a>
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

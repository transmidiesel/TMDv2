<?php
$paginaActual = basename($_SERVER['PHP_SELF']);
?>
<aside class="admin-sidebar">
  <div class="brand">
    <div class="brand-title">Transmidiesel</div>
    <div class="brand-sub">Panel de administración</div>
  </div>

  <nav>
    <a href="index.php" class="<?= $paginaActual === 'index.php' ? 'active' : '' ?>">
      <span class="nav-icon">🏠</span> Dashboard
    </a>
    <a href="noticias.php" class="<?= in_array($paginaActual, ['noticias.php','noticia-form.php']) ? 'active' : '' ?>">
      <span class="nav-icon">📰</span> Noticias
    </a>
    <a href="marcas.php" class="<?= in_array($paginaActual, ['marcas.php','marca-form.php']) ? 'active' : '' ?>">
      <span class="nav-icon">🏷️</span> Marcas
    </a>
    <a href="certificaciones.php" class="<?= in_array($paginaActual, ['certificaciones.php','certificacion-form.php']) ? 'active' : '' ?>">
      <span class="nav-icon">📜</span> Certificaciones
    </a>
    <a href="productos.php" class="<?= in_array($paginaActual, ['productos.php','productos-form.php']) ? 'active' : '' ?>">
      <span class="nav-icon">📦</span> Productos
    </a>
    <a href="configuracion.php" class="<?= $paginaActual === 'configuracion.php' ? 'active' : '' ?>">
      <span class="nav-icon">⚙️</span> Configuración
    </a>
  </nav>
</aside>
<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/db.php';

// Estadísticas rápidas
$stats = [
    'noticias'        => $pdo->query("SELECT COUNT(*) FROM noticias")->fetchColumn(),
    'marcas'          => $pdo->query("SELECT COUNT(*) FROM marcas")->fetchColumn(),
    'certificaciones' => $pdo->query("SELECT COUNT(*) FROM certificaciones")->fetchColumn(),
    'productos'       => $pdo->query("SELECT COUNT(*) FROM productos")->fetchColumn(),
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Panel · Transmidiesel</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="admin.css">
</head>
<body>

<?php include 'partials/sidebar.php'; ?>

<main class="admin-main">
  <header class="admin-topbar">
    <h1>Bienvenido, <?= htmlspecialchars($_SESSION['admin_nombre']) ?></h1>
    <a href="logout.php" class="btn-logout">Cerrar sesión</a>
  </header>

  <section class="admin-content">
    <div class="dashboard-grid">
      <a href="noticias.php" class="dash-card">
        <div class="dash-icon">📰</div>
        <div class="dash-num"><?= $stats['noticias'] ?></div>
        <div class="dash-label">Noticias</div>
        <div class="dash-action">Gestionar →</div>
      </a>

      <a href="marcas.php" class="dash-card">
        <div class="dash-icon">🏷️</div>
        <div class="dash-num"><?= $stats['marcas'] ?></div>
        <div class="dash-label">Marcas aliadas</div>
        <div class="dash-action">Gestionar →</div>
      </a>

      <a href="certificaciones.php" class="dash-card">
        <div class="dash-icon">📜</div>
        <div class="dash-num"><?= $stats['certificaciones'] ?></div>
        <div class="dash-label">Certificaciones</div>
        <div class="dash-action">Gestionar →</div>
      </a>

      <a href="productos.php" class="dash-card">
        <div class="dash-icon">📦</div>
        <div class="dash-num"><?= $stats['productos'] ?></div>
        <div class="dash-label">Productos</div>
        <div class="dash-action">Gestionar →</div>
      </a>

      <a href="configuracion.php" class="dash-card">
        <div class="dash-icon">⚙️</div>
        <div class="dash-num">—</div>
        <div class="dash-label">Configuración</div>
        <div class="dash-action">Ajustar →</div>
      </a>
    </div>

    <div class="info-box">
      <h3>💡 ¿Qué puedo hacer desde aquí?</h3>
      <ul>
        <li>Agregar, editar, activar/desactivar y eliminar noticias</li>
        <li>Subir y gestionar los logos de las marcas aliadas</li>
        <li>Actualizar los enlaces de validación de las certificaciones</li>
        <li>Gestionar el catálogo de productos (con precios en USD)</li>
        <li>Cambiar los logos del navbar y datos de contacto</li>
      </ul>
      <p style="color:#5a5f66;font-size:13px;margin-top:12px;">
        Todo lo que cambies aquí se refleja automáticamente en el sitio público.
      </p>
    </div>
  </section>
</main>

</body>
</html>
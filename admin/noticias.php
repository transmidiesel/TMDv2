<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/db.php';

$mensaje = '';
$tipo    = '';

if (isset($_GET['ok'])) {
    $mensaje = match($_GET['ok']) {
        'creada'    => 'Noticia creada correctamente.',
        'editada'   => 'Noticia actualizada correctamente.',
        'eliminada' => 'Noticia eliminada.',
        default     => ''
    };
    $tipo = 'success';
}
if (isset($_GET['error'])) {
    $mensaje = 'Ocurrió un error: ' . htmlspecialchars($_GET['error']);
    $tipo = 'error';
}

$noticias = $pdo->query("SELECT * FROM noticias ORDER BY orden ASC, id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Noticias · Panel Transmidiesel</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="admin.css">
</head>
<body>

<?php include 'partials/sidebar.php'; ?>

<main class="admin-main">
  <header class="admin-topbar">
    <h1>Gestión de Noticias</h1>
    <a href="logout.php" class="btn-logout">Cerrar sesión</a>
  </header>

  <section class="admin-content">

    <?php if ($mensaje): ?>
      <div class="alert alert-<?= $tipo ?>"><?= $mensaje ?></div>
    <?php endif; ?>

    <div class="page-head">
      <div>
        <h2>Todas las noticias</h2>
        <p class="sub"><?= count($noticias) ?> noticia(s) registrada(s) · El número se asigna solo según la posición</p>
      </div>
      <a href="noticia-form.php" class="btn btn-primary">+ Nueva noticia</a>
    </div>

    <?php if (empty($noticias)): ?>
      <div class="table-wrap">
        <div class="empty-state">
          <div class="icon">📰</div>
          <h3>No hay noticias todavía</h3>
          <p>Crea la primera con el botón "+ Nueva noticia".</p>
        </div>
      </div>
    <?php else: ?>
      <div class="table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th style="width:100px;">Imagen</th>
              <th>Título</th>
              <th style="width:110px;text-align:center;">Posición</th>
              <th style="width:110px;">Estado</th>
              <th style="width:160px;text-align:right;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($noticias as $n): ?>
              <tr>
                <td class="td-thumb">
                  <?php if ($n['imagen']): ?>
                    <img src="../<?= htmlspecialchars($n['imagen']) ?>"
                         alt=""
                         onerror="this.style.display='none';this.parentNode.innerHTML='<span style=\'color:#999;font-size:11px;\'>Sin imagen</span>';">
                  <?php else: ?>
                    <span style="color:#999;font-size:11px;">Sin imagen</span>
                  <?php endif; ?>
                </td>
                <td class="td-title">
                  <?= htmlspecialchars($n['titulo']) ?>
                  <?php if ($n['enlace']): ?>
                    <small>🔗 <?= htmlspecialchars($n['enlace']) ?></small>
                  <?php endif; ?>
                </td>
                <td style="text-align:center;">
                  <span class="badge badge-order">#<?= (int)$n['orden'] ?></span>
                </td>
                <td>
                  <?php if ($n['activa']): ?>
                    <span class="badge badge-active">Activa</span>
                  <?php else: ?>
                    <span class="badge badge-inactive">Oculta</span>
                  <?php endif; ?>
                </td>
                <td>
                  <div class="td-actions">
                    <a href="noticia-form.php?id=<?= $n['id'] ?>" class="btn btn-secondary btn-sm">Editar</a>
                    <a href="noticia-eliminar.php?id=<?= $n['id'] ?>"
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('¿Seguro que quieres eliminar esta noticia?\n\n<?= htmlspecialchars(addslashes($n['titulo'])) ?>');">
                       Eliminar
                    </a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>

  </section>
</main>

</body>
</html>
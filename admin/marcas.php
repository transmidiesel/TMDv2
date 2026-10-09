<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/db.php';

$mensaje = '';
$tipo    = '';

if (isset($_GET['ok'])) {
    $mensaje = match($_GET['ok']) {
        'creada'    => 'Marca creada correctamente.',
        'editada'   => 'Marca actualizada correctamente.',
        'eliminada' => 'Marca eliminada.',
        default     => ''
    };
    $tipo = 'success';
}
if (isset($_GET['error'])) {
    $mensaje = 'Ocurrió un error: ' . htmlspecialchars($_GET['error']);
    $tipo = 'error';
}

$marcas = $pdo->query("SELECT * FROM marcas ORDER BY orden ASC, id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Marcas · Panel Transmidiesel</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="admin.css">
</head>
<body>

<?php include 'partials/sidebar.php'; ?>

<main class="admin-main">
  <header class="admin-topbar">
    <h1>Gestión de Marcas</h1>
    <a href="logout.php" class="btn-logout">Cerrar sesión</a>
  </header>

  <section class="admin-content">

    <?php if ($mensaje): ?>
      <div class="alert alert-<?= $tipo ?>"><?= $mensaje ?></div>
    <?php endif; ?>

    <div class="page-head">
      <div>
        <h2>Marcas aliadas</h2>
        <p class="sub"><?= count($marcas) ?> marca(s) registrada(s)</p>
      </div>
      <a href="marca-form.php" class="btn btn-primary">+ Nueva marca</a>
    </div>

    <?php if (empty($marcas)): ?>
      <div class="table-wrap">
        <div class="empty-state">
          <div class="icon">🏷️</div>
          <h3>No hay marcas todavía</h3>
          <p>Crea la primera con el botón "+ Nueva marca".</p>
        </div>
      </div>
    <?php else: ?>
      <div class="table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th style="width:100px;">Logo</th>
              <th>Nombre</th>
              <th style="width:100px;">Orden</th>
              <th style="width:110px;">Estado</th>
              <th style="width:160px;text-align:right;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($marcas as $m): ?>
              <tr>
                <td class="td-thumb">
                  <img src="../<?= htmlspecialchars($m['logo']) ?>" alt=""
                       onerror="this.style.display='none';this.parentNode.innerHTML='<span style=\'color:#999;font-size:11px;\'>Sin logo</span>';">
                </td>
                <td class="td-title">
                  <?= htmlspecialchars($m['nombre']) ?>
                  <?php if ($m['enlace']): ?>
                    <small>🔗 <?= htmlspecialchars($m['enlace']) ?></small>
                  <?php endif; ?>
                </td>
                <td><span class="badge badge-order"><?= (int)$m['orden'] ?></span></td>
                <td>
                  <?php if ($m['activa']): ?>
                    <span class="badge badge-active">Activa</span>
                  <?php else: ?>
                    <span class="badge badge-inactive">Oculta</span>
                  <?php endif; ?>
                </td>
                <td>
                  <div class="td-actions">
                    <a href="marca-form.php?id=<?= $m['id'] ?>" class="btn btn-secondary btn-sm">Editar</a>
                    <a href="marca-eliminar.php?id=<?= $m['id'] ?>"
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('¿Eliminar esta marca?\n\n<?= htmlspecialchars(addslashes($m['nombre'])) ?>');">
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
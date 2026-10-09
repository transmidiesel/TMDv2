<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/db.php';

$mensaje = $_GET['msg'] ?? '';
$tipo    = $_GET['tipo'] ?? '';

// Eliminar producto
if (isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
    $id = (int)$_GET['eliminar'];

    $stmt = $pdo->prepare("SELECT imagen FROM productos WHERE id = ?");
    $stmt->execute([$id]);
    $img = $stmt->fetchColumn();

    if ($img && str_starts_with($img, 'uploads/productos/')) {
        $ruta = __DIR__ . '/../' . $img;
        if (file_exists($ruta)) @unlink($ruta);
    }

    $stmt = $pdo->prepare("DELETE FROM productos WHERE id = ?");
    $stmt->execute([$id]);

    header('Location: productos.php?msg=Producto eliminado&tipo=success');
    exit;
}

// Toggle activo/inactivo
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $pdo->prepare("UPDATE productos SET activo = NOT activo WHERE id = ?")->execute([$id]);
    header('Location: productos.php?msg=Estado actualizado&tipo=success');
    exit;
}

// Cargar productos
$productos = $pdo->query("
    SELECT * FROM productos
    ORDER BY orden ASC, id DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Productos · Panel Transmidiesel</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="admin.css">
</head>
<body>
<?php include 'partials/sidebar.php'; ?>

<main class="admin-main">
  <header class="admin-topbar">
    <h1>Productos</h1>
    <a href="logout.php" class="btn-logout">Cerrar sesión</a>
  </header>

  <section class="admin-content">

    <?php if ($mensaje): ?>
      <div class="alert alert-<?= htmlspecialchars($tipo) ?>">
        <?= htmlspecialchars($mensaje) ?>
      </div>
    <?php endif; ?>

    <div class="page-head">
      <div>
        <h2>Catálogo de productos</h2>
        <p class="sub">Los precios se guardan en USD y se convierten automáticamente en el sitio público.</p>
      </div>
      <a href="productos-form.php" class="btn btn-primary">
        <span>＋</span> Nuevo producto
      </a>
    </div>

    <?php if (empty($productos)): ?>
      <div class="table-wrap">
        <div class="empty-state">
          <div class="icon">📦</div>
          <h3>No hay productos todavía</h3>
          <p>Crea el primer producto con el botón "Nuevo producto".</p>
        </div>
      </div>
    <?php else: ?>
      <div class="table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th style="width:100px;">Imagen</th>
              <th>Producto</th>
              <th style="width:120px;">Precio USD</th>
              <th style="width:120px;">Categoría</th>
              <th style="width:80px;">Orden</th>
              <th style="width:110px;">Estado</th>
              <th style="width:210px; text-align:right;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($productos as $p): ?>
              <tr>
                <td class="td-thumb">
                  <?php if ($p['imagen']): ?>
                    <img src="../<?= htmlspecialchars($p['imagen']) ?>" alt=""
                         onerror="this.style.opacity='0.3';this.style.filter='grayscale(1)';">
                  <?php else: ?>
                    <div style="width:70px;height:45px;background:#f0f2f5;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#999;font-size:10px;">Sin img</div>
                  <?php endif; ?>
                </td>
                <td class="td-title">
                  <?= htmlspecialchars($p['nombre']) ?>
                  <?php if ($p['descripcion']): ?>
                    <small><?= htmlspecialchars(mb_substr($p['descripcion'], 0, 70)) ?><?= mb_strlen($p['descripcion']) > 70 ? '…' : '' ?></small>
                  <?php endif; ?>
                </td>
                <td>
                  <strong style="color:var(--accent);font-family:'Space Grotesk';">
                    US$ <?= number_format((float)$p['precio_usd'], 2) ?>
                  </strong>
                </td>
                <td>
                  <span class="badge badge-order"><?= htmlspecialchars($p['categoria'] ?: 'general') ?></span>
                </td>
                <td><?= (int)$p['orden'] ?></td>
                <td>
                  <?php if ($p['activo']): ?>
                    <span class="badge badge-active">Activo</span>
                  <?php else: ?>
                    <span class="badge badge-inactive">Inactivo</span>
                  <?php endif; ?>
                </td>
                <td>
                  <div class="td-actions">
                    <a href="?toggle=<?= $p['id'] ?>" class="btn btn-secondary btn-sm"
                       title="<?= $p['activo'] ? 'Desactivar' : 'Activar' ?>">
                      <?= $p['activo'] ? '🚫' : '✅' ?>
                    </a>
                    <a href="productos-form.php?id=<?= $p['id'] ?>" class="btn btn-secondary btn-sm">
                      ✏️ Editar
                    </a>
                    <a href="?eliminar=<?= $p['id'] ?>"
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('¿Eliminar este producto? Esta acción no se puede deshacer.');">
                      🗑
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
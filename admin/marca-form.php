<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/db.php';

$esEdicion = false;
$marca = [
    'id'     => 0,
    'nombre' => '',
    'logo'   => '',
    'enlace' => '',
    'orden'  => 0,
    'activa' => 1,
];

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM marcas WHERE id = ?");
    $stmt->execute([(int)$_GET['id']]);
    $fila = $stmt->fetch();
    if ($fila) {
        $marca     = $fila;
        $esEdicion = true;
    } else {
        header('Location: marcas.php?error=Marca no encontrada');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $esEdicion ? 'Editar' : 'Nueva' ?> marca · Panel Transmidiesel</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="admin.css">
</head>
<body>

<?php include 'partials/sidebar.php'; ?>

<main class="admin-main">
  <header class="admin-topbar">
    <h1><?= $esEdicion ? 'Editar marca' : 'Nueva marca' ?></h1>
    <a href="logout.php" class="btn-logout">Cerrar sesión</a>
  </header>

  <section class="admin-content">

    <div class="page-head">
      <div>
        <h2><?= $esEdicion ? 'Editar marca' : 'Crear marca' ?></h2>
        <p class="sub">Los cambios se reflejan automáticamente en el sitio.</p>
      </div>
      <a href="marcas.php" class="btn btn-secondary">← Volver</a>
    </div>

    <form class="form-card" action="marca-guardar.php" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="id" value="<?= (int)$marca['id'] ?>">

      <div class="form-group">
        <label for="nombre">Nombre <span style="color:#d9383a">*</span></label>
        <input type="text" id="nombre" name="nombre" required maxlength="100"
               value="<?= htmlspecialchars($marca['nombre']) ?>"
               placeholder="Ej: Duramax">
      </div>

      <div class="form-group">
        <label for="enlace">Enlace destino <span class="help">(opcional)</span></label>
        <input type="text" id="enlace" name="enlace" maxlength="500"
               value="<?= htmlspecialchars($marca['enlace']) ?>"
               placeholder="marcas/duramax/  ó  https://sitio-externo.com">
        <small class="help">Puede ser una ruta interna (marcas/duramax/) o una URL externa.</small>
      </div>

      <div class="form-row-2">
        <div class="form-group">
          <label for="orden">Orden</label>
          <input type="number" id="orden" name="orden" min="0"
                 value="<?= (int)$marca['orden'] ?>">
          <small class="help">Números más bajos aparecen primero.</small>
        </div>

        <div class="form-group" style="display:flex;align-items:flex-end;">
          <div class="checkbox-row" style="width:100%;">
            <input type="checkbox" id="activa" name="activa" value="1"
                   <?= $marca['activa'] ? 'checked' : '' ?>>
            <label for="activa">Marca activa (visible en el sitio)</label>
          </div>
        </div>
      </div>

      <div class="form-group">
        <label for="logo">Logo <span class="help">(png con fondo transparente recomendado)</span></label>
        <input type="file" id="logo" name="logo" accept="image/*"
               onchange="previewImage(event)">
        <input type="hidden" name="logo_actual" value="<?= htmlspecialchars($marca['logo']) ?>">
        <div class="img-preview" id="imgPreview" style="width:200px;min-height:100px;">
          <?php if ($marca['logo']): ?>
            <img src="../<?= htmlspecialchars($marca['logo']) ?>" alt="Preview"
                 onerror="this.parentNode.innerHTML='<div class=\'no-img\'>Logo no encontrado</div>';">
          <?php else: ?>
            <div class="no-img">Sin logo</div>
          <?php endif; ?>
        </div>
      </div>

      <div class="form-actions">
        <a href="marcas.php" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary">
          <?= $esEdicion ? 'Guardar cambios' : 'Crear marca' ?>
        </button>
      </div>

    </form>

  </section>
</main>

<script>
function previewImage(e) {
  const file = e.target.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = ev => {
    document.getElementById('imgPreview').innerHTML =
      '<img src="' + ev.target.result + '" alt="Preview">';
  };
  reader.readAsDataURL(file);
}
</script>

</body>
</html>
<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/db.php';

$esEdicion = false;
$cert = [
    'id'                => 0,
    'nombre'            => '',
    'logo'              => '',
    'enlace_validacion' => '',
    'orden'             => 0,
    'activa'            => 1,
];

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM certificaciones WHERE id = ?");
    $stmt->execute([(int)$_GET['id']]);
    $fila = $stmt->fetch();
    if ($fila) { $cert = $fila; $esEdicion = true; }
    else { header('Location: certificaciones.php?error=No encontrada'); exit; }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $esEdicion ? 'Editar' : 'Nueva' ?> certificación</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="admin.css">
</head>
<body>
<?php include 'partials/sidebar.php'; ?>

<main class="admin-main">
  <header class="admin-topbar">
    <h1><?= $esEdicion ? 'Editar certificación' : 'Nueva certificación' ?></h1>
    <a href="logout.php" class="btn-logout">Cerrar sesión</a>
  </header>

  <section class="admin-content">
    <div class="page-head">
      <div>
        <h2><?= $esEdicion ? 'Editar' : 'Crear' ?> certificación</h2>
      </div>
      <a href="certificaciones.php" class="btn btn-secondary">← Volver</a>
    </div>

    <form class="form-card" action="certificacion-guardar.php" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="id" value="<?= (int)$cert['id'] ?>">

      <div class="form-group">
        <label for="nombre">Nombre <span style="color:#d9383a">*</span></label>
        <input type="text" id="nombre" name="nombre" required maxlength="100"
               value="<?= htmlspecialchars($cert['nombre']) ?>"
               placeholder="Ej: ISO 9001">
      </div>

      <div class="form-group">
        <label for="enlace_validacion">Enlace de validación <span class="help">(opcional)</span></label>
        <input type="url" id="enlace_validacion" name="enlace_validacion" maxlength="500"
               value="<?= htmlspecialchars($cert['enlace_validacion']) ?>"
               placeholder="https://www.sgs.com/en/certified-clients...">
        <small class="help">Al hacer clic en el logo, se abrirá este enlace.</small>
      </div>

      <div class="form-row-2">
        <div class="form-group">
          <label for="orden">Orden</label>
          <input type="number" id="orden" name="orden" min="0" value="<?= (int)$cert['orden'] ?>">
        </div>

        <div class="form-group" style="display:flex;align-items:flex-end;">
          <div class="checkbox-row" style="width:100%;">
            <input type="checkbox" id="activa" name="activa" value="1" <?= $cert['activa'] ? 'checked' : '' ?>>
            <label for="activa">Activa (visible en el sitio)</label>
          </div>
        </div>
      </div>

      <div class="form-group">
        <label for="logo">Logo <span class="help">(png con fondo transparente)</span></label>
        <input type="file" id="logo" name="logo" accept="image/*" onchange="previewImage(event)">
        <input type="hidden" name="logo_actual" value="<?= htmlspecialchars($cert['logo']) ?>">
        <div class="img-preview" id="imgPreview" style="width:200px;min-height:100px;">
          <?php if ($cert['logo']): ?>
            <img src="../<?= htmlspecialchars($cert['logo']) ?>" alt="Preview"
                 onerror="this.parentNode.innerHTML='<div class=\'no-img\'>No encontrado</div>';">
          <?php else: ?>
            <div class="no-img">Sin logo</div>
          <?php endif; ?>
        </div>
      </div>

      <div class="form-actions">
        <a href="certificaciones.php" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary"><?= $esEdicion ? 'Guardar cambios' : 'Crear' ?></button>
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
    document.getElementById('imgPreview').innerHTML = '<img src="' + ev.target.result + '" alt="Preview">';
  };
  reader.readAsDataURL(file);
}
</script>
</body>
</html>
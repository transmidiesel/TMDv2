<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$esEdicion = $id > 0;
$mensaje = '';
$tipo = '';

// Valores por defecto
$producto = [
    'nombre'      => '',
    'descripcion' => '',
    'imagen'      => '',
    'precio_usd'  => '0.00',
    'categoria'   => 'general',
    'activo'      => 1,
    'orden'       => 0,
];

// Si es edición, cargar datos
if ($esEdicion) {
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
    $stmt->execute([$id]);
    $encontrado = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$encontrado) {
        header('Location: productos.php?msg=Producto no encontrado&tipo=error');
        exit;
    }
    $producto = $encontrado;
}

// Procesar POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $nombre      = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $descripcion = str_replace(["\r\n", "\r"], "\n", $descripcion); // normalizar saltos
        $precio_usd  = (float)str_replace(',', '.', $_POST['precio_usd'] ?? '0');
        $categoria   = trim($_POST['categoria'] ?? 'general');
        $activo      = isset($_POST['activo']) ? 1 : 0;
        $orden       = (int)($_POST['orden'] ?? 0);
        $imagen      = $producto['imagen'] ?? '';

        if ($nombre === '') throw new Exception('El nombre es obligatorio.');
        if ($precio_usd < 0) throw new Exception('El precio no puede ser negativo.');

        // ---------- Subida de imagen con validación cuadrada 1080x1080 ----------
        if (!empty($_FILES['imagen']['name']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $archivo = $_FILES['imagen'];

            if ($archivo['size'] > 3 * 1024 * 1024) {
                throw new Exception('La imagen no puede superar 3 MB.');
            }

            $tipos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
            $info  = @getimagesize($archivo['tmp_name']);

            if (!$info) {
                throw new Exception('El archivo no es una imagen válida.');
            }

            $ancho = (int)$info[0];
            $alto  = (int)$info[1];
            $mime  = $info['mime'] ?? mime_content_type($archivo['tmp_name']);

            if (!isset($tipos[$mime])) {
                throw new Exception('Formato no permitido. Usa JPG, PNG o WEBP.');
            }

            // ✅ VALIDACIÓN: la imagen debe ser cuadrada
            if ($ancho !== $alto) {
                throw new Exception(
                    "La imagen debe ser cuadrada (mismo ancho y alto). " .
                    "La tuya mide {$ancho}×{$alto} px. " .
                    "Redimensiónala a 1080×1080 px antes de subirla."
                );
            }

            // ✅ VALIDACIÓN: mínimo recomendado 600x600 (para que no se vea pixelada)
            if ($ancho < 600) {
                throw new Exception(
                    "La imagen es muy pequeña ({$ancho}×{$alto} px). " .
                    "Usa mínimo 600×600 px, idealmente 1080×1080 px."
                );
            }

            // ✅ VALIDACIÓN: máximo 3000x3000 (para que no ocupe demasiado)
            if ($ancho > 3000) {
                throw new Exception(
                    "La imagen es muy grande ({$ancho}×{$alto} px). " .
                    "Usa máximo 3000×3000 px, idealmente 1080×1080 px."
                );
            }

            // Guardar archivo (manteniendo dimensiones originales)
            $ext = $tipos[$mime];
            $nombreArchivo = 'producto_' . date('Ymd_His') . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
            $carpeta = __DIR__ . '/../uploads/productos/';

            if (!is_dir($carpeta)) mkdir($carpeta, 0755, true);

            if (!move_uploaded_file($archivo['tmp_name'], $carpeta . $nombreArchivo)) {
                throw new Exception('No se pudo guardar la imagen.');
            }

            // Borrar imagen anterior si estaba en uploads
            if ($imagen && str_starts_with($imagen, 'uploads/productos/')) {
                $rutaVieja = __DIR__ . '/../' . $imagen;
                if (file_exists($rutaVieja)) @unlink($rutaVieja);
            }

            $imagen = 'uploads/productos/' . $nombreArchivo;
        }

        // Borrar imagen si se marcó
        if (!empty($_POST['borrar_imagen']) && $imagen) {
            if (str_starts_with($imagen, 'uploads/productos/')) {
                $ruta = __DIR__ . '/../' . $imagen;
                if (file_exists($ruta)) @unlink($ruta);
            }
            $imagen = '';
        }

        // Insertar o actualizar
        if ($esEdicion) {
            $stmt = $pdo->prepare("
                UPDATE productos
                SET nombre=?, descripcion=?, imagen=?, precio_usd=?, categoria=?, activo=?, orden=?
                WHERE id=?
            ");
            $stmt->execute([$nombre, $descripcion, $imagen, $precio_usd, $categoria, $activo, $orden, $id]);
            header('Location: productos.php?msg=Producto actualizado&tipo=success');
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO productos (nombre, descripcion, imagen, precio_usd, categoria, activo, orden)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$nombre, $descripcion, $imagen, $precio_usd, $categoria, $activo, $orden]);
            header('Location: productos.php?msg=Producto creado&tipo=success');
        }
        exit;

    } catch (Exception $e) {
        $mensaje = $e->getMessage();
        $tipo = 'error';

        $producto = array_merge($producto, [
            'nombre'      => $_POST['nombre'] ?? '',
            'descripcion' => $_POST['descripcion'] ?? '',
            'precio_usd'  => $_POST['precio_usd'] ?? '0',
            'categoria'   => $_POST['categoria'] ?? 'general',
            'activo'      => isset($_POST['activo']) ? 1 : 0,
            'orden'       => $_POST['orden'] ?? 0,
        ]);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $esEdicion ? 'Editar' : 'Nuevo' ?> producto · Panel Transmidiesel</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="admin.css">
<style>
  .img-requisito {
    background: #fff8e1;
    border: 1px solid #ffcc02;
    border-radius: 10px;
    padding: 12px 16px;
    font-size: 13px;
    color: #6d4c00;
    margin-bottom: 14px;
    line-height: 1.55;
  }
  .img-requisito strong { color: #8a6200; }
  .img-requisito ul { margin: 6px 0 0 18px; padding: 0; }
  .img-requisito li { margin-bottom: 3px; }

  .img-preview {
    position: relative;
  }
  .img-preview .img-dim {
    position: absolute;
    top: 8px;
    left: 8px;
    background: rgba(0,0,0,0.75);
    color: #fff;
    font-size: 11px;
    font-weight: 600;
    padding: 4px 8px;
    border-radius: 6px;
    letter-spacing: .3px;
  }
  .img-preview .img-dim.bad {
    background: rgba(211,47,47,0.9);
  }
  .img-preview .img-dim.ok {
    background: rgba(46,125,50,0.9);
  }
</style>
</head>
<body>
<?php include 'partials/sidebar.php'; ?>

<main class="admin-main">
  <header class="admin-topbar">
    <h1><?= $esEdicion ? 'Editar producto' : 'Nuevo producto' ?></h1>
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
        <h2><?= $esEdicion ? 'Editar producto #' . $id : 'Crear nuevo producto' ?></h2>
        <p class="sub">El precio se guarda en USD y se convierte automáticamente en el sitio público.</p>
      </div>
      <a href="productos.php" class="btn btn-secondary">← Volver al listado</a>
    </div>

    <form class="form-card" method="POST" enctype="multipart/form-data" id="productoForm">

      <div class="form-group">
        <label for="nombre">Nombre del producto *</label>
        <input type="text" id="nombre" name="nombre" required
               value="<?= htmlspecialchars($producto['nombre']) ?>"
               placeholder="Ej: BOX COOLER">
      </div>

      <div class="form-group">
        <label for="descripcion">Descripción</label>
        <textarea id="descripcion" name="descripcion"
                  placeholder="Describe brevemente el producto..."><?= htmlspecialchars($producto['descripcion']) ?></textarea>
      </div>

      <div class="form-row-2">
        <div class="form-group">
          <label for="precio_usd">Precio en USD *</label>
          <input type="number" id="precio_usd" name="precio_usd" step="0.01" min="0" required
                 value="<?= htmlspecialchars($producto['precio_usd']) ?>"
                 placeholder="450.00">
        </div>

        <div class="form-group">
          <label for="categoria">Categoría</label>
          <select id="categoria" name="categoria">
            <?php
              $cats = ['general','naval','industrial','agricola','petrolero','minero'];
              $catActual = $producto['categoria'] ?: 'general';
              foreach ($cats as $c) {
                  $sel = $c === $catActual ? ' selected' : '';
                  echo "<option value=\"$c\"$sel>" . ucfirst($c) . "</option>";
              }
            ?>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label for="imagen">Imagen del producto</label>

        <div class="img-requisito">
          <strong>⚠️ Requisitos de la imagen</strong>
          <ul>
            <li>Debe ser <strong>cuadrada</strong> (mismo ancho y alto)</li>
            <li>Tamaño recomendado: <strong>1080 × 1080 px</strong></li>
            <li>Mínimo: 600 × 600 px &nbsp;·&nbsp; Máximo: 3000 × 3000 px</li>
            <li>Formatos: JPG, PNG o WEBP</li>
            <li>Peso máximo: 3 MB</li>
            <li>La imagen <strong>no se recorta ni se deforma</strong>: se muestra tal cual la subas</li>
          </ul>
        </div>

        <input type="file" id="imagen" name="imagen" accept="image/*">

        <div class="img-preview" id="imgPreview">
          <?php if (!empty($producto['imagen'])): ?>
            <img src="../<?= htmlspecialchars($producto['imagen']) ?>" alt=""
                 onerror="this.parentNode.innerHTML='<div class=\'no-img\'>Imagen no encontrada</div>';">
          <?php else: ?>
            <div class="no-img">Sin imagen</div>
          <?php endif; ?>
        </div>

        <div id="imgFeedback" style="margin-top:10px;font-size:13px;"></div>

        <?php if (!empty($producto['imagen'])): ?>
          <label class="checkbox-row" style="margin-top:12px;width:fit-content;">
            <input type="checkbox" name="borrar_imagen" value="1">
            <span>Eliminar imagen actual</span>
          </label>
        <?php endif; ?>
      </div>

      <div class="form-row-2">
        <div class="form-group">
          <label for="orden">Orden <span class="help">(menor = primero)</span></label>
          <input type="number" id="orden" name="orden" min="0"
                 value="<?= (int)$producto['orden'] ?>">
        </div>

        <div class="form-group">
          <label>Estado</label>
          <label class="checkbox-row">
            <input type="checkbox" name="activo" value="1" <?= $producto['activo'] ? 'checked' : '' ?>>
            <span>Producto activo (visible en el sitio)</span>
          </label>
        </div>
      </div>

      <div class="form-actions">
        <a href="productos.php" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary" id="btnGuardar">
          <?= $esEdicion ? '💾 Guardar cambios' : '＋ Crear producto' ?>
        </button>
      </div>

    </form>
  </section>
</main>

<script>
(function() {
  const inputImagen = document.getElementById('imagen');
  const preview     = document.getElementById('imgPreview');
  const feedback    = document.getElementById('imgFeedback');
  const btnGuardar  = document.getElementById('btnGuardar');
  if (!inputImagen) return;

  inputImagen.addEventListener('change', function(e) {
    const file = e.target.files[0];
    feedback.innerHTML = '';
    btnGuardar.disabled = false;

    if (!file) return;

    const reader = new FileReader();
    reader.onload = function(ev) {
      const img = new Image();
      img.onload = function() {
        const w = img.naturalWidth;
        const h = img.naturalHeight;
        const esCuadrada = w === h;
        const minOk = w >= 600 && h >= 600;
        const maxOk = w <= 3000 && h <= 3000;

        // Mostrar preview con badge de dimensiones
        preview.innerHTML = `
          <img src="${ev.target.result}" alt="">
          <span class="img-dim ${esCuadrada ? 'ok' : 'bad'}">${w} × ${h} px</span>
        `;

        // Mensaje según validación
        if (!esCuadrada) {
          feedback.innerHTML = `<span style="color:#b71c1c;font-weight:600;">
            ❌ La imagen NO es cuadrada (${w}×${h}). Debe tener el mismo ancho y alto.
            Redimensiónala a 1080×1080 px antes de guardar.</span>`;
          btnGuardar.disabled = true;
        } else if (!minOk) {
          feedback.innerHTML = `<span style="color:#b71c1c;font-weight:600;">
            ❌ La imagen es muy pequeña (${w}×${h}). Usa mínimo 600×600 px.</span>`;
          btnGuardar.disabled = true;
        } else if (!maxOk) {
          feedback.innerHTML = `<span style="color:#b71c1c;font-weight:600;">
            ❌ La imagen es muy grande (${w}×${h}). Usa máximo 3000×3000 px.</span>`;
          btnGuardar.disabled = true;
        } else {
          feedback.innerHTML = `<span style="color:#1b5e20;font-weight:600;">
            ✅ Imagen válida (${w}×${h})</span>`;
        }
      };
      img.onerror = function() {
        feedback.innerHTML = '<span style="color:#b71c1c;">❌ No se pudo leer la imagen.</span>';
      };
      img.src = ev.target.result;
    };
    reader.readAsDataURL(file);
  });
})();
</script>

</body>
</html>
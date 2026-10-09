<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/db.php';

$mensaje = '';
$tipo    = '';

// Guardar cambios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Valores de texto
        $camposTexto = ['telefono_nacional', 'telefono_whatsapp', 'email_contacto', 'mision_texto', 'vision_texto'];
        foreach ($camposTexto as $clave) {
            if (isset($_POST[$clave])) {
                $stmt = $pdo->prepare("UPDATE configuracion SET valor = ? WHERE clave = ?");
                $stmt->execute([trim($_POST[$clave]), $clave]);
            }
        }

        // Subida de logos
        foreach (['logo_principal', 'logo_scrolled'] as $clave) {
            if (!empty($_FILES[$clave]['name']) && $_FILES[$clave]['error'] === UPLOAD_ERR_OK) {
                $archivo = $_FILES[$clave];
                if ($archivo['size'] > 3 * 1024 * 1024) throw new Exception("$clave supera 3 MB");

                $tipos = ['image/jpeg','image/png','image/webp','image/gif','image/svg+xml'];
                $info = @getimagesize($archivo['tmp_name']);
                $mime = $info['mime'] ?? mime_content_type($archivo['tmp_name']);
                if (!in_array($mime, $tipos)) throw new Exception("Tipo no permitido en $clave");

                $ext = match($mime) {
                    'image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp',
                    'image/gif' => 'gif', 'image/svg+xml' => 'svg', default => 'png'
                };
                $nombreArchivo = $clave . '_' . date('Ymd_His') . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
                $carpeta = __DIR__ . '/../uploads/logos/';
                if (!is_dir($carpeta)) mkdir($carpeta, 0755, true);

                if (!move_uploaded_file($archivo['tmp_name'], $carpeta . $nombreArchivo)) {
                    throw new Exception("No se pudo guardar $clave");
                }
                $nuevaRuta = 'uploads/logos/' . $nombreArchivo;

                // Borrar logo viejo si estaba en uploads
                $stmt = $pdo->prepare("SELECT valor FROM configuracion WHERE clave = ?");
                $stmt->execute([$clave]);
                $vieja = $stmt->fetchColumn();
                if ($vieja && str_starts_with($vieja, 'uploads/logos/')) {
                    $rutaVieja = __DIR__ . '/../' . $vieja;
                    if (file_exists($rutaVieja)) @unlink($rutaVieja);
                }

                $stmt = $pdo->prepare("UPDATE configuracion SET valor = ? WHERE clave = ?");
                $stmt->execute([$nuevaRuta, $clave]);
            }
        }

        $mensaje = 'Configuración guardada correctamente.';
        $tipo = 'success';

    } catch (Exception $e) {
        $mensaje = 'Error: ' . $e->getMessage();
        $tipo = 'error';
    }
}

// Cargar configuración actual
$config = [];
foreach ($pdo->query("SELECT clave, valor, descripcion FROM configuracion") as $row) {
    $config[$row['clave']] = $row;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Configuración · Panel Transmidiesel</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="admin.css">
</head>
<body>
<?php include 'partials/sidebar.php'; ?>

<main class="admin-main">
  <header class="admin-topbar">
    <h1>Configuración general</h1>
    <a href="logout.php" class="btn-logout">Cerrar sesión</a>
  </header>

  <section class="admin-content">

    <?php if ($mensaje): ?>
      <div class="alert alert-<?= $tipo ?>"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <div class="page-head">
      <div>
        <h2>Valores globales del sitio</h2>
        <p class="sub">Estos datos se usan en el navbar, footer y módulo de WhatsApp.</p>
      </div>
    </div>

    <form class="form-card" method="POST" enctype="multipart/form-data" style="max-width:900px;">

      <h3 style="font-family:'Space Grotesk';margin:0 0 20px;color:var(--accent);">🎨 Logos del navbar</h3>

      <div class="form-row-2">
        <div class="form-group">
          <label for="logo_principal">Logo principal <span class="help">(visible arriba del hero)</span></label>
          <input type="file" id="logo_principal" name="logo_principal" accept="image/*">
          <div class="img-preview" style="width:180px;min-height:80px;">
            <?php if (!empty($config['logo_principal']['valor'])): ?>
              <img src="../<?= htmlspecialchars($config['logo_principal']['valor']) ?>" alt=""
                   onerror="this.parentNode.innerHTML='<div class=\'no-img\'>No encontrado</div>';">
            <?php else: ?>
              <div class="no-img">Sin logo</div>
            <?php endif; ?>
          </div>
          <small class="help" style="display:block;margin-top:6px;">
            Actual: <?= htmlspecialchars($config['logo_principal']['valor'] ?? '—') ?>
          </small>
        </div>

        <div class="form-group">
          <label for="logo_scrolled">Logo al hacer scroll</label>
          <input type="file" id="logo_scrolled" name="logo_scrolled" accept="image/*">
          <div class="img-preview" style="width:180px;min-height:80px;">
            <?php if (!empty($config['logo_scrolled']['valor'])): ?>
              <img src="../<?= htmlspecialchars($config['logo_scrolled']['valor']) ?>" alt=""
                   onerror="this.parentNode.innerHTML='<div class=\'no-img\'>No encontrado</div>';">
            <?php else: ?>
              <div class="no-img">Sin logo</div>
            <?php endif; ?>
          </div>
          <small class="help" style="display:block;margin-top:6px;">
            Actual: <?= htmlspecialchars($config['logo_scrolled']['valor'] ?? '—') ?>
          </small>
        </div>
      </div>

      <h3 style="font-family:'Space Grotesk';margin:30px 0 20px;color:var(--accent);">📞 Datos de contacto</h3>

      <div class="form-group">
        <label for="telefono_nacional">Línea Nacional (texto que se muestra)</label>
        <input type="text" id="telefono_nacional" name="telefono_nacional"
               value="<?= htmlspecialchars($config['telefono_nacional']['valor'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label for="telefono_whatsapp">WhatsApp <span class="help">(solo números, sin + ni espacios)</span></label>
        <input type="text" id="telefono_whatsapp" name="telefono_whatsapp"
               value="<?= htmlspecialchars($config['telefono_whatsapp']['valor'] ?? '') ?>"
               placeholder="573168775212">
      </div>

      <div class="form-group">
        <label for="email_contacto">Email de contacto</label>
        <input type="email" id="email_contacto" name="email_contacto"
               value="<?= htmlspecialchars($config['email_contacto']['valor'] ?? '') ?>">
      </div>

      <h3 style="font-family:'Space Grotesk';margin:30px 0 20px;color:var(--accent);">📝 Misión y Visión (Quiénes Somos)</h3>

      <div class="form-group">
        <label for="mision_texto">Misión</label>
        <textarea id="mision_texto" name="mision_texto" rows="6" style="width:100%;padding:12px;border-radius:10px;border:1px solid var(--border);font-family:inherit;font-size:14px;resize:vertical;"><?= htmlspecialchars($config['mision_texto']['valor'] ?? '') ?></textarea>
      </div>

      <div class="form-group">
        <label for="vision_texto">Visión</label>
        <textarea id="vision_texto" name="vision_texto" rows="6" style="width:100%;padding:12px;border-radius:10px;border:1px solid var(--border);font-family:inherit;font-size:14px;resize:vertical;"><?= htmlspecialchars($config['vision_texto']['valor'] ?? '') ?></textarea>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-primary">Guardar configuración</button>
      </div>

    </form>
  </section>
</main>
</body>
</html>
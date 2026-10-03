<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: certificaciones.php'); exit; }

$id          = (int)($_POST['id'] ?? 0);
$nombre      = trim($_POST['nombre'] ?? '');
$enlace      = trim($_POST['enlace_validacion'] ?? '');
$orden       = (int)($_POST['orden'] ?? 0);
$activa      = isset($_POST['activa']) ? 1 : 0;
$logoActual  = trim($_POST['logo_actual'] ?? '');

if ($nombre === '') {
    header('Location: certificacion-form.php?error=Nombre obligatorio' . ($id ? "&id=$id" : ''));
    exit;
}

$rutaLogo = $logoActual;
if (!empty($_FILES['logo']['name']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
    $archivo = $_FILES['logo'];
    if ($archivo['size'] > 3 * 1024 * 1024) {
        header('Location: certificacion-form.php?error=Logo muy grande' . ($id ? "&id=$id" : ''));
        exit;
    }
    $tipos = ['image/jpeg','image/png','image/webp','image/gif','image/svg+xml'];
    $info = @getimagesize($archivo['tmp_name']);
    $mime = $info['mime'] ?? mime_content_type($archivo['tmp_name']);
    if (!in_array($mime, $tipos)) {
        header('Location: certificacion-form.php?error=Tipo no permitido' . ($id ? "&id=$id" : ''));
        exit;
    }
    $ext = match($mime) {
        'image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp',
        'image/gif' => 'gif', 'image/svg+xml' => 'svg', default => 'png'
    };
    $nombreArchivo = 'cert_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $carpeta = __DIR__ . '/../uploads/logos/';
    if (!is_dir($carpeta)) mkdir($carpeta, 0755, true);

    if (!move_uploaded_file($archivo['tmp_name'], $carpeta . $nombreArchivo)) {
        header('Location: certificacion-form.php?error=No se pudo guardar' . ($id ? "&id=$id" : ''));
        exit;
    }
    $rutaLogo = 'uploads/logos/' . $nombreArchivo;

    if ($id && $logoActual && str_starts_with($logoActual, 'uploads/logos/')) {
        $viejo = __DIR__ . '/../' . $logoActual;
        if (file_exists($viejo)) @unlink($viejo);
    }
}

try {
    if ($id > 0) {
        $stmt = $pdo->prepare("UPDATE certificaciones SET nombre=?, logo=?, enlace_validacion=?, orden=?, activa=? WHERE id=?");
        $stmt->execute([$nombre, $rutaLogo, $enlace ?: null, $orden, $activa, $id]);
        header('Location: certificaciones.php?ok=editada');
    } else {
        if ($rutaLogo === '') {
            header('Location: certificacion-form.php?error=Debes subir un logo');
            exit;
        }
        $stmt = $pdo->prepare("INSERT INTO certificaciones (nombre, logo, enlace_validacion, orden, activa) VALUES (?,?,?,?,?)");
        $stmt->execute([$nombre, $rutaLogo, $enlace ?: null, $orden, $activa]);
        header('Location: certificaciones.php?ok=creada');
    }
    exit;
} catch (Exception $e) {
    header('Location: certificaciones.php?error=' . urlencode($e->getMessage()));
    exit;
}
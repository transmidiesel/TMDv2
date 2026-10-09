<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: marcas.php');
    exit;
}

$id         = (int)($_POST['id'] ?? 0);
$nombre     = trim($_POST['nombre'] ?? '');
$enlace     = trim($_POST['enlace'] ?? '');
$orden      = (int)($_POST['orden'] ?? 0);
$activa     = isset($_POST['activa']) ? 1 : 0;
$logoActual = trim($_POST['logo_actual'] ?? '');

if ($nombre === '') {
    header('Location: marca-form.php?error=El nombre es obligatorio' . ($id ? "&id=$id" : ''));
    exit;
}

// ---------- Subida de logo ----------
$rutaLogo = $logoActual;
if (!empty($_FILES['logo']['name']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
    $archivo = $_FILES['logo'];

    if ($archivo['size'] > 3 * 1024 * 1024) {
        header('Location: marca-form.php?error=El logo supera 3 MB' . ($id ? "&id=$id" : ''));
        exit;
    }

    $tiposPermitidos = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/svg+xml'];
    $info = @getimagesize($archivo['tmp_name']);
    $mime = $info['mime'] ?? mime_content_type($archivo['tmp_name']);

    if (!in_array($mime, $tiposPermitidos)) {
        header('Location: marca-form.php?error=Tipo de imagen no permitido' . ($id ? "&id=$id" : ''));
        exit;
    }

    $ext = match($mime) {
        'image/jpeg'    => 'jpg',
        'image/png'     => 'png',
        'image/webp'    => 'webp',
        'image/gif'     => 'gif',
        'image/svg+xml' => 'svg',
        default         => 'png'
    };
    $nombreArchivo  = 'marca_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $carpetaDestino = __DIR__ . '/../uploads/logos/';

    if (!is_dir($carpetaDestino)) {
        mkdir($carpetaDestino, 0755, true);
    }

    $destinoFisico = $carpetaDestino . $nombreArchivo;
    if (!move_uploaded_file($archivo['tmp_name'], $destinoFisico)) {
        header('Location: marca-form.php?error=No se pudo guardar el logo' . ($id ? "&id=$id" : ''));
        exit;
    }

    $rutaLogo = 'uploads/logos/' . $nombreArchivo;

    // Borrar logo anterior SOLO si estaba en uploads/logos/
    if ($id && $logoActual && str_starts_with($logoActual, 'uploads/logos/')) {
        $viejo = __DIR__ . '/../' . $logoActual;
        if (file_exists($viejo)) @unlink($viejo);
    }
}

// ---------- Insertar o actualizar ----------
try {
    if ($id > 0) {
        $stmt = $pdo->prepare("
            UPDATE marcas SET nombre = ?, logo = ?, enlace = ?, orden = ?, activa = ?
            WHERE id = ?
        ");
        $stmt->execute([$nombre, $rutaLogo, $enlace ?: null, $orden, $activa, $id]);
        header('Location: marcas.php?ok=editada');
    } else {
        if ($rutaLogo === '') {
            header('Location: marca-form.php?error=Debes subir un logo para la nueva marca');
            exit;
        }
        $stmt = $pdo->prepare("
            INSERT INTO marcas (nombre, logo, enlace, orden, activa)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$nombre, $rutaLogo, $enlace ?: null, $orden, $activa]);
        header('Location: marcas.php?ok=creada');
    }
    exit;

} catch (Exception $e) {
    header('Location: marcas.php?error=' . urlencode($e->getMessage()));
    exit;
}
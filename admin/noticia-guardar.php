<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: noticias.php');
    exit;
}

// ---------- Recoger y validar datos ----------
$id           = (int)($_POST['id'] ?? 0);
$titulo       = trim($_POST['titulo'] ?? '');
$descripcion  = trim($_POST['descripcion'] ?? '');
$enlace       = trim($_POST['enlace'] ?? '');
$posicion     = (int)($_POST['orden'] ?? 0);   // la "posición deseada" que elige el usuario
$activa       = isset($_POST['activa']) ? 1 : 0;
$imagenActual = trim($_POST['imagen_actual'] ?? '');

if ($titulo === '' || $descripcion === '') {
    header('Location: noticia-form.php?error=Campos obligatorios vacíos' . ($id ? "&id=$id" : ''));
    exit;
}

// ---------- Manejar subida de imagen ----------
$rutaImagen = $imagenActual;
if (!empty($_FILES['imagen']['name']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $archivo = $_FILES['imagen'];

    if ($archivo['size'] > 3 * 1024 * 1024) {
        header('Location: noticia-form.php?error=La imagen supera 3 MB' . ($id ? "&id=$id" : ''));
        exit;
    }

    $tiposPermitidos = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $info = @getimagesize($archivo['tmp_name']);
    if (!$info || !in_array($info['mime'], $tiposPermitidos)) {
        header('Location: noticia-form.php?error=Tipo de imagen no permitido' . ($id ? "&id=$id" : ''));
        exit;
    }

    $ext = match($info['mime']) {
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
        default      => 'jpg'
    };
    $nombreArchivo  = 'noticia_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $carpetaDestino = __DIR__ . '/../uploads/noticias/';

    if (!is_dir($carpetaDestino)) {
        mkdir($carpetaDestino, 0755, true);
    }

    $destinoFisico = $carpetaDestino . $nombreArchivo;
    if (!move_uploaded_file($archivo['tmp_name'], $destinoFisico)) {
        header('Location: noticia-form.php?error=No se pudo guardar la imagen' . ($id ? "&id=$id" : ''));
        exit;
    }

    $rutaImagen = 'uploads/noticias/' . $nombreArchivo;

    if ($id && $imagenActual && str_starts_with($imagenActual, 'uploads/noticias/')) {
        $rutaVieja = __DIR__ . '/../' . $imagenActual;
        if (file_exists($rutaVieja)) @unlink($rutaVieja);
    }
}

// =========================================================
// GUARDADO CON REPOSICIONAMIENTO CORRECTO
// =========================================================

try {
    $pdo->beginTransaction();

    if ($id > 0) {
        // ----- CASO EDITAR -----

        // 1) Actualizar los datos básicos (sin tocar `orden` todavía)
        $stmt = $pdo->prepare("
            UPDATE noticias
            SET titulo = ?, descripcion = ?, imagen = ?, enlace = ?, activa = ?
            WHERE id = ?
        ");
        $stmt->execute([$titulo, $descripcion, $rutaImagen ?: null, $enlace ?: null, $activa, $id]);

        // 2) Sacar temporalmente esta noticia de la lista,
        //    poniéndole un orden muy alto para que no estorbe
        $stmt = $pdo->prepare("UPDATE noticias SET orden = 99999 WHERE id = ?");
        $stmt->execute([$id]);

        // 3) Renumerar las demás de 1 a N (ya sin esta)
        $ids = $pdo->query("SELECT id FROM noticias WHERE id != $id ORDER BY orden ASC, id ASC")
                   ->fetchAll(PDO::FETCH_COLUMN);
        $update = $pdo->prepare("UPDATE noticias SET orden = ? WHERE id = ?");
        $pos = 1;
        foreach ($ids as $nid) {
            $update->execute([$pos, $nid]);
            $pos++;
        }

        // 4) Calcular la posición final deseada (clamp entre 1 y total+1)
        $total = count($ids);
        if ($posicion < 1) $posicion = $total + 1;
        if ($posicion > $total + 1) $posicion = $total + 1;

        // 5) Hacer hueco: subir en 1 el orden de todas las noticias
        //    cuya posición sea >= a la deseada
        $stmt = $pdo->prepare("
            UPDATE noticias
            SET orden = orden + 1
            WHERE id != ? AND orden >= ?
        ");
        $stmt->execute([$id, $posicion]);

        // 6) Colocar nuestra noticia en la posición deseada
        $stmt = $pdo->prepare("UPDATE noticias SET orden = ? WHERE id = ?");
        $stmt->execute([$posicion, $id]);

    } else {
        // ----- CASO CREAR -----

        // 1) Calcular cuántas noticias hay actualmente
        $total = (int)$pdo->query("SELECT COUNT(*) FROM noticias")->fetchColumn();

        // 2) Clamp de la posición deseada
        if ($posicion < 1) $posicion = $total + 1;
        if ($posicion > $total + 1) $posicion = $total + 1;

        // 3) Hacer hueco: subir en 1 el orden de todas las que estén en esa posición o superior
        $stmt = $pdo->prepare("UPDATE noticias SET orden = orden + 1 WHERE orden >= ?");
        $stmt->execute([$posicion]);

        // 4) Insertar la nueva noticia en esa posición
        $stmt = $pdo->prepare("
            INSERT INTO noticias (titulo, descripcion, imagen, enlace, orden, activa)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$titulo, $descripcion, $rutaImagen ?: null, $enlace ?: null, $posicion, $activa]);
    }

    // 5) Renumerar TODO de 1 a N para garantizar que no queden huecos
    renumerarNoticias($pdo);

    $pdo->commit();

    header('Location: noticias.php?ok=' . ($id > 0 ? 'editada' : 'creada'));
    exit;

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    header('Location: noticias.php?error=' . urlencode($e->getMessage()));
    exit;
}

/**
 * Renumera el campo "orden" de todas las noticias de 1 a N
 * según el orden actual (respetando el orden existente y el id como desempate).
 * Así nunca quedan huecos ni duplicados.
 */
function renumerarNoticias(PDO $pdo): void {
    $stmt = $pdo->query("SELECT id FROM noticias ORDER BY orden ASC, id ASC");
    $ids  = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $update = $pdo->prepare("UPDATE noticias SET orden = ? WHERE id = ?");
    $pos = 1;
    foreach ($ids as $noticiaId) {
        $update->execute([$pos, $noticiaId]);
        $pos++;
    }
}
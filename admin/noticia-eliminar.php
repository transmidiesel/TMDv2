<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/db.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: noticias.php?error=ID inválido');
    exit;
}

// Recuperar la imagen para borrarla del disco
$stmt = $pdo->prepare("SELECT imagen FROM noticias WHERE id = ?");
$stmt->execute([$id]);
$noticia = $stmt->fetch();

if (!$noticia) {
    header('Location: noticias.php?error=Noticia no encontrada');
    exit;
}

// Borrar del disco si la imagen fue subida por el panel
if ($noticia['imagen'] && str_starts_with($noticia['imagen'], 'uploads/noticias/')) {
    $ruta = __DIR__ . '/../' . $noticia['imagen'];
    if (file_exists($ruta)) @unlink($ruta);
}

// Borrar registro
$stmt = $pdo->prepare("DELETE FROM noticias WHERE id = ?");
$stmt->execute([$id]);

// ---------------------------------------------------------
// Renumerar las noticias restantes de 1 a N
// para que no queden huecos en la numeración
// ---------------------------------------------------------
$stmt = $pdo->query("SELECT id FROM noticias ORDER BY orden ASC, id ASC");
$ids  = $stmt->fetchAll(PDO::FETCH_COLUMN);

$update = $pdo->prepare("UPDATE noticias SET orden = ? WHERE id = ?");
$pos = 1;
foreach ($ids as $noticiaId) {
    $update->execute([$pos, $noticiaId]);
    $pos++;
}

header('Location: noticias.php?ok=eliminada');
exit;
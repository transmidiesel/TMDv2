<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/db.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: marcas.php?error=ID inválido');
    exit;
}

$stmt = $pdo->prepare("SELECT logo FROM marcas WHERE id = ?");
$stmt->execute([$id]);
$marca = $stmt->fetch();

if (!$marca) {
    header('Location: marcas.php?error=Marca no encontrada');
    exit;
}

// Borrar logo del disco si fue subido por el panel
if ($marca['logo'] && str_starts_with($marca['logo'], 'uploads/logos/')) {
    $ruta = __DIR__ . '/../' . $marca['logo'];
    if (file_exists($ruta)) @unlink($ruta);
}

$stmt = $pdo->prepare("DELETE FROM marcas WHERE id = ?");
$stmt->execute([$id]);

header('Location: marcas.php?ok=eliminada');
exit;
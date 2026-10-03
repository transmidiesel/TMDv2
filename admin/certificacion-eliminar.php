<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/db.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: certificaciones.php?error=ID inválido'); exit; }

$stmt = $pdo->prepare("SELECT logo FROM certificaciones WHERE id = ?");
$stmt->execute([$id]);
$cert = $stmt->fetch();
if (!$cert) { header('Location: certificaciones.php?error=No encontrada'); exit; }

if ($cert['logo'] && str_starts_with($cert['logo'], 'uploads/logos/')) {
    $ruta = __DIR__ . '/../' . $cert['logo'];
    if (file_exists($ruta)) @unlink($ruta);
}

$stmt = $pdo->prepare("DELETE FROM certificaciones WHERE id = ?");
$stmt->execute([$id]);

header('Location: certificaciones.php?ok=eliminada');
exit;
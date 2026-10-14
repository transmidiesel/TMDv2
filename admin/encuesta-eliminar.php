<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/db.php';

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: encuestas.php?error=ID inválido');
    exit;
}

// Verificar que exista
$stmt = $pdo->prepare("SELECT id FROM encuesta_respuestas WHERE id = ?");
$stmt->execute([$id]);
if (!$stmt->fetch()) {
    header('Location: encuestas.php?error=Encuesta no encontrada');
    exit;
}

// Eliminar (los valores se borran por ON DELETE CASCADE)
$stmt = $pdo->prepare("DELETE FROM encuesta_respuestas WHERE id = ?");
$stmt->execute([$id]);

header('Location: encuestas.php?ok=eliminada');
exit;
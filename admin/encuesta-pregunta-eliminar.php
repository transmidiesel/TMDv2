<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/db.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: encuesta-preguntas.php?error=ID inválido');
    exit;
}

// Verificar que exista
$stmt = $pdo->prepare("SELECT id FROM encuesta_preguntas WHERE id = ?");
$stmt->execute([$id]);
if (!$stmt->fetch()) {
    header('Location: encuesta-preguntas.php?error=Pregunta no encontrada');
    exit;
}

// Eliminar (los valores asociados se borran por ON DELETE CASCADE)
$stmt = $pdo->prepare("DELETE FROM encuesta_preguntas WHERE id = ?");
$stmt->execute([$id]);

header('Location: encuesta-preguntas.php?ok=eliminada');
exit;
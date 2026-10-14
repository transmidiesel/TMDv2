<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/db.php';

$id     = (int)($_GET['id'] ?? 0);
$accion = $_GET['accion'] ?? '';

if ($id <= 0 || !in_array($accion, ['aprobar', 'rechazar'], true)) {
    header('Location: encuestas.php?error=Parámetros inválidos');
    exit;
}

// Verificar que exista
$stmt = $pdo->prepare("SELECT id FROM encuesta_respuestas WHERE id = ?");
$stmt->execute([$id]);
if (!$stmt->fetch()) {
    header('Location: encuestas.php?error=Encuesta no encontrada');
    exit;
}

$aprobada = $accion === 'aprobar' ? 1 : 0;

$stmt = $pdo->prepare("
    UPDATE encuesta_respuestas
    SET aprobada = :aprobada,
        motivo_revision = CASE WHEN :aprobada2 = 1 THEN NULL ELSE motivo_revision END
    WHERE id = :id
");
$stmt->execute([
    ':aprobada'  => $aprobada,
    ':aprobada2' => $aprobada,
    ':id'        => $id,
]);

$ok = $accion === 'aprobar' ? 'aprobada' : 'rechazada';
header("Location: encuestas.php?ok=$ok");
exit;
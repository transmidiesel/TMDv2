<?php
// =========================================================
// api/encuesta-preguntas.php
// Devuelve las preguntas activas de la encuesta de satisfacción.
// Público — no requiere autenticación.
// Método: GET
// Respuesta: JSON con { ok: true, preguntas: [...] }
// =========================================================

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');

// Solo permitir GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
    exit;
}

require_once __DIR__ . '/../includes/db.php';

try {
    $stmt = $pdo->query("
        SELECT
            id,
            orden,
            texto,
            tipo,
            escala_min,
            escala_max,
            escala_label_min,
            escala_label_max,
            opciones,
            obligatoria
        FROM encuesta_preguntas
        WHERE activa = 1
        ORDER BY orden ASC, id ASC
    ");

    $preguntas = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Convertir tipos para que el JSON sea limpio
        $pregunta = [
            'id'               => (int) $row['id'],
            'orden'            => (int) $row['orden'],
            'texto'            => $row['texto'],
            'tipo'             => $row['tipo'],
            'obligatoria'      => (bool) $row['obligatoria'],
            'escala_min'       => $row['escala_min']       !== null ? (int) $row['escala_min'] : null,
            'escala_max'       => $row['escala_max']       !== null ? (int) $row['escala_max'] : null,
            'escala_label_min' => $row['escala_label_min'],
            'escala_label_max' => $row['escala_label_max'],
        ];

        // Si es tipo opción múltiple, decodificar el JSON de opciones
        if ($row['tipo'] === 'opcion' && !empty($row['opciones'])) {
            $decoded = json_decode($row['opciones'], true);
            $pregunta['opciones'] = is_array($decoded) ? $decoded : [];
        }

        $preguntas[] = $pregunta;
    }

    echo json_encode([
        'ok'        => true,
        'total'     => count($preguntas),
        'preguntas' => $preguntas,
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'ok'    => false,
        'error' => 'Error al cargar las preguntas',
        // En desarrollo puedes descomentar la siguiente línea para ver el error:
        // 'detalle' => $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
}
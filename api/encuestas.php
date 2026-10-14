<?php
// =========================================================
// api/encuestas.php
// Devuelve las encuestas aprobadas para el carrusel público.
// Calcula el promedio de las preguntas de escala de cada encuesta.
// Público — no requiere autenticación.
// Método: GET
// Respuesta: JSON { ok: true, encuestas: [...] }
// =========================================================

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
    exit;
}

require_once __DIR__ . '/../includes/db.php';

try {
    // 1) Traer las encuestas aprobadas (máximo 50 para no sobrecargar)
    $stmt = $pdo->query("
        SELECT id, nombre, fecha
        FROM encuesta_respuestas
        WHERE aprobada = 1
        ORDER BY fecha DESC
        LIMIT 50
    ");
    $respuestas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($respuestas)) {
        echo json_encode(['ok' => true, 'total' => 0, 'encuestas' => []], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // 2) Traer todos los valores de esas respuestas de una sola consulta
    $ids = array_column($respuestas, 'id');
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $stmtValores = $pdo->prepare("
        SELECT
            v.respuesta_id,
            v.pregunta_id,
            v.valor_escala,
            v.valor_texto,
            p.tipo,
            p.texto
        FROM encuesta_valores v
        INNER JOIN encuesta_preguntas p ON p.id = v.pregunta_id
        WHERE v.respuesta_id IN ($placeholders)
    ");
    $stmtValores->execute($ids);
    $valores = $stmtValores->fetchAll(PDO::FETCH_ASSOC);

    // 3) Agrupar valores por respuesta_id
    $valoresPorRespuesta = [];
    foreach ($valores as $v) {
        $rid = (int) $v['respuesta_id'];
        if (!isset($valoresPorRespuesta[$rid])) {
            $valoresPorRespuesta[$rid] = [];
        }
        $valoresPorRespuesta[$rid][] = $v;
    }

    // 4) Armar la respuesta final
    $encuestas = [];
    foreach ($respuestas as $r) {
        $rid = (int) $r['id'];
        $vals = $valoresPorRespuesta[$rid] ?? [];

        $sumaEscalas = 0;
        $countEscalas = 0;
        $comentario = '';

        foreach ($vals as $v) {
            // Promedio de escalas
            if ($v['tipo'] === 'escala' && $v['valor_escala'] !== null) {
                $sumaEscalas += (int) $v['valor_escala'];
                $countEscalas++;
            }

            // Comentario: la primera pregunta de texto que contenga palabras clave
            if ($v['tipo'] === 'texto' && !empty($v['valor_texto'])) {
                if (preg_match('/expectativa|mejora|comentario|observaci|sugerencia/i', $v['texto'])) {
                    if ($comentario === '') {
                        $comentario = $v['valor_texto'];
                    }
                }
            }
        }

        $promedio = $countEscalas > 0
            ? round($sumaEscalas / $countEscalas, 1)
            : 0.0;

        // Generar inicial para avatar (primera letra del nombre)
        $inicial = mb_strtoupper(mb_substr($r['nombre'], 0, 1, 'UTF-8'), 'UTF-8');

        $encuestas[] = [
            'id'        => $rid,
            'nombre'    => $r['nombre'],
            'inicial'   => $inicial,
            'promedio'  => $promedio,
            'comentario'=> $comentario,
            'fecha'     => $r['fecha'],
        ];
    }

    echo json_encode([
        'ok'        => true,
        'total'     => count($encuestas),
        'encuestas' => $encuestas,
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'ok'    => false,
        'error' => 'Error al cargar las encuestas',
        // 'detalle' => $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
}
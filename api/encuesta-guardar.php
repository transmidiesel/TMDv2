<?php
// =========================================================
// api/encuesta-guardar.php
// Guarda una respuesta de la encuesta de satisfacción.
// Aplica filtro automático de moderación:
//   - Si el comentario contiene palabras prohibidas → pendiente
//   - Si alguna calificación es muy baja (<=3) → pendiente
//   - Si todo está bien → se publica automáticamente
// Método: POST (JSON)
// =========================================================

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

// Solo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
    exit;
}

require_once __DIR__ . '/../includes/db.php';

// =========================================================
// CONFIGURACIÓN DEL FILTRO DE MODERACIÓN
// =========================================================

// Palabras prohibidas (se buscan como substrings en el comentario, sin distinguir mayúsculas)
// Edita esta lista libremente.
$PALABRAS_PROHIBIDAS = [
    'puta', 'puto', 'mierda', 'verga', 'pene', 'pija', 'coño', 'carajo',
    'hijueputa', 'malparido', 'gonorrea', 'marica', 'carechimba',
    'estafa', 'estafadores', 'ladrones', 'basura', 'porqueria', 'porquería',
    'hp', 'hpta', 'pvt4', 'mrda',
];

// Calificación mínima aceptable en cualquier escala (si es menor o igual, va a revisión)
$CALIFICACION_MINIMA_OK = 3;

// =========================================================
// LEER Y VALIDAR INPUT
// =========================================================

$raw = file_get_contents('php://input');
$payload = json_decode($raw, true);

if (!is_array($payload) || !isset($payload['respuestas']) || !is_array($payload['respuestas'])) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Datos inválidos']);
    exit;
}

$respuestas = $payload['respuestas'];

// =========================================================
// CARGAR LAS PREGUNTAS ACTIVAS DE LA DB
// =========================================================

try {
    $stmt = $pdo->query("
        SELECT id, texto, tipo, obligatoria, escala_min, escala_max
        FROM encuesta_preguntas
        WHERE activa = 1
        ORDER BY orden ASC, id ASC
    ");
    $preguntas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($preguntas)) {
        throw new Exception('No hay preguntas activas');
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Error al cargar preguntas']);
    exit;
}

// =========================================================
// MAPEAR RESPUESTAS POR pregunta_id
// =========================================================

$respuestasPorPregunta = [];
foreach ($respuestas as $r) {
    if (!isset($r['pregunta_id'])) continue;
    $pid = (int) $r['pregunta_id'];
    $respuestasPorPregunta[$pid] = $r;
}

// =========================================================
// VALIDAR Y EXTRAER CAMPOS ESPECIALES
// =========================================================

$nombre    = 'Cliente anónimo';
$documento = null;
$valores   = [];      // [ { pregunta_id, valor_escala, valor_texto }, ... ]
$comentario = '';     // Para el filtro de moderación
$peorEscala = 10;     // Para el filtro de moderación

foreach ($preguntas as $p) {
    $pid    = (int) $p['id'];
    $tipo   = $p['tipo'];
    $oblig  = (bool) $p['obligatoria'];
    $resp   = $respuestasPorPregunta[$pid] ?? null;

    $valorEscala = null;
    $valorTexto  = null;

    if ($tipo === 'texto' || $tipo === 'opcion') {
        $valorTexto = $resp ? trim((string)($resp['valor'] ?? '')) : '';

        if ($oblig && $valorTexto === '') {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'Falta responder: ' . $p['texto']]);
            exit;
        }

        // Detectar si esta pregunta es el "nombre" o el "documento"
        $textoLower = mb_strtolower($p['texto'], 'UTF-8');
        if (strpos($textoLower, 'nombre') !== false && $nombre === 'Cliente anónimo' && $valorTexto !== '') {
            $nombre = mb_substr($valorTexto, 0, 120, 'UTF-8');
        }
        if ((strpos($textoLower, 'documento') !== false
             || strpos($textoLower, 'nit') !== false
             || strpos($textoLower, 'cedula') !== false
             || strpos($textoLower, 'cédula') !== false)
            && $documento === null && $valorTexto !== '') {
            $documento = mb_substr($valorTexto, 0, 60, 'UTF-8');
        }

        // Si la pregunta contiene "expectativa", "mejora", "comentario" u "observación" → tratarla como comentario
        if (preg_match('/expectativa|mejora|comentario|observaci|sugerencia/i', $p['texto'])) {
            $comentario = $valorTexto;
        }

    } else if ($tipo === 'escala') {
        $valorEscala = $resp ? (int)($resp['valor'] ?? 0) : null;
        $min = (int)($p['escala_min'] ?? 1);
        $max = (int)($p['escala_max'] ?? 10);

        if ($oblig && ($valorEscala === null || $valorEscala < $min || $valorEscala > $max)) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'Calificación inválida en: ' . $p['texto']]);
            exit;
        }

        if ($valorEscala !== null && $valorEscala < $peorEscala) {
            $peorEscala = $valorEscala;
        }
    }

    $valores[] = [
        'pregunta_id'  => $pid,
        'valor_escala' => $valorEscala,
        'valor_texto'  => $valorTexto,
    ];
}

// =========================================================
// FILTRO DE MODERACIÓN
// =========================================================

$aprobada       = 1;
$motivoRevision = null;

// A) Palabras prohibidas en el comentario
if ($comentario !== '') {
    $comentarioLower = mb_strtolower($comentario, 'UTF-8');
    foreach ($PALABRAS_PROHIBIDAS as $palabra) {
        if (mb_strpos($comentarioLower, $palabra) !== false) {
            $aprobada = 0;
            $motivoRevision = 'Contiene palabra filtrada: "' . $palabra . '"';
            break;
        }
    }
}

// B) Calificación muy baja
if ($aprobada === 1 && $peorEscala <= $CALIFICACION_MINIMA_OK) {
    $aprobada = 0;
    $motivoRevision = 'Calificación baja (' . $peorEscala . ') — requiere revisión manual';
}

// =========================================================
// GUARDAR EN DB
// =========================================================

try {
    $pdo->beginTransaction();

    // 1) Insertar la respuesta general
    $stmt = $pdo->prepare("
        INSERT INTO encuesta_respuestas
            (nombre, documento, aprobada, motivo_revision, ip, user_agent)
        VALUES
            (:nombre, :documento, :aprobada, :motivo, :ip, :ua)
    ");
    $stmt->execute([
        ':nombre'    => $nombre,
        ':documento' => $documento,
        ':aprobada'  => $aprobada,
        ':motivo'    => $motivoRevision,
        ':ip'        => $_SERVER['REMOTE_ADDR'] ?? null,
        ':ua'        => isset($_SERVER['HTTP_USER_AGENT']) ? mb_substr($_SERVER['HTTP_USER_AGENT'], 0, 255, 'UTF-8') : null,
    ]);

    $respuestaId = (int) $pdo->lastInsertId();

    // 2) Insertar los valores individuales
    $stmtValor = $pdo->prepare("
        INSERT INTO encuesta_valores
            (respuesta_id, pregunta_id, valor_escala, valor_texto)
        VALUES
            (:rid, :pid, :escala, :texto)
    ");
    foreach ($valores as $v) {
        $stmtValor->execute([
            ':rid'    => $respuestaId,
            ':pid'    => $v['pregunta_id'],
            ':escala' => $v['valor_escala'],
            ':texto'  => $v['valor_texto'],
        ]);
    }

    $pdo->commit();

    // =========================================================
    // RESPUESTA
    // =========================================================

    if ($aprobada === 1) {
        echo json_encode([
            'ok'      => true,
            'aprobada'=> true,
            'mensaje' => 'Tu encuesta fue publicada. ¡Gracias por tu opinión!',
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            'ok'      => true,
            'aprobada'=> false,
            'mensaje' => 'Gracias por tu opinión. Tu respuesta será revisada por nuestro equipo antes de publicarse.',
        ], JSON_UNESCAPED_UNICODE);
    }

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode([
        'ok'    => false,
        'error' => 'Error al guardar la encuesta. Inténtalo de nuevo.',
        // En desarrollo puedes descomentar para ver el error real:
        // 'detalle' => $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
}
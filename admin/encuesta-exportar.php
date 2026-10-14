<?php
// =========================================================
// admin/encuesta-exportar.php
// Exporta todas las encuestas a un archivo CSV compatible con Excel.
// - Incluye TODAS las preguntas (columnas dinámicas)
// - Agrega columnas calculadas: promedio, estado, fecha
// - Descarga directa con nombre encuesta_satisfaccion_YYYY-MM-DD.csv
// =========================================================

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/db.php';

// ---------- Filtro por estado (opcional) ----------
$filtro = $_GET['filtro'] ?? 'todas';
$where  = '';
if ($filtro === 'aprobadas')  $where = 'WHERE r.aprobada = 1';
if ($filtro === 'pendientes') $where = 'WHERE r.aprobada = 0';

// ---------- 1) Traer preguntas ordenadas ----------
$preguntas = $pdo->query("
    SELECT id, orden, texto, tipo
    FROM encuesta_preguntas
    ORDER BY orden ASC, id ASC
")->fetchAll(PDO::FETCH_ASSOC);

// ---------- 2) Traer respuestas ----------
$stmt = $pdo->query("
    SELECT r.id, r.nombre, r.documento, r.aprobada, r.motivo_revision, r.fecha, r.ip
    FROM encuesta_respuestas r
    $where
    ORDER BY r.fecha DESC
");
$respuestas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ---------- 3) Traer todos los valores (en una sola consulta) ----------
$valoresPorRespuesta = [];
if (!empty($respuestas)) {
    $ids = array_column($respuestas, 'id');
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $stmtV = $pdo->prepare("
        SELECT respuesta_id, pregunta_id, valor_escala, valor_texto
        FROM encuesta_valores
        WHERE respuesta_id IN ($placeholders)
    ");
    $stmtV->execute($ids);
    while ($row = $stmtV->fetch(PDO::FETCH_ASSOC)) {
        $rid = (int) $row['respuesta_id'];
        $pid = (int) $row['pregunta_id'];
        if (!isset($valoresPorRespuesta[$rid])) {
            $valoresPorRespuesta[$rid] = [];
        }
        $valoresPorRespuesta[$rid][$pid] = $row;
    }
}

// ---------- 4) Armar nombre del archivo ----------
$nombreArchivo = 'encuesta_satisfaccion_' . date('Y-m-d_His') . '.csv';

// ---------- 5) Enviar cabeceras para descarga ----------
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');
header('Pragma: no-cache');
header('Expires: 0');

// ---------- 6) Abrir salida y escribir BOM UTF-8 (para que Excel respete tildes y ñ) ----------
$out = fopen('php://output', 'w');

// BOM UTF-8 → Excel abre el archivo respetando los caracteres especiales
fwrite($out, "\xEF\xBB\xBF");

// ---------- 7) Cabecera del CSV ----------
$cabecera = [
    'ID',
    'Fecha',
    'Nombre',
    'Documento',
    'Estado',
    'Motivo revisión',
    'Promedio',
];

// Agregar una columna por cada pregunta
foreach ($preguntas as $p) {
    // Limpiar el texto: quitar saltos de línea, cortar si es muy largo
    $label = preg_replace('/\s+/', ' ', $p['texto']);
    $label = mb_strimwidth($label, 0, 60, '…', 'UTF-8');
    $cabecera[] = $label;
}

// Columnas extra al final
$cabecera[] = 'IP';
fputcsv($out, $cabecera, ';');

// ---------- 8) Filas de datos ----------
foreach ($respuestas as $r) {
    $rid   = (int) $r['id'];
    $vals  = $valoresPorRespuesta[$rid] ?? [];

    // Calcular promedio de escalas
    $sumaEscalas   = 0;
    $countEscalas  = 0;
    foreach ($vals as $v) {
        if ($v['valor_escala'] !== null) {
            $sumaEscalas += (int) $v['valor_escala'];
            $countEscalas++;
        }
    }
    $promedio = $countEscalas > 0 ? round($sumaEscalas / $countEscalas, 1) : '';

    // Estado legible
    $estado = $r['aprobada'] ? 'Publicada' : 'Pendiente';

    // Fila base
    $fila = [
        $rid,
        date('d/m/Y H:i', strtotime($r['fecha'])),
        $r['nombre'],
        $r['documento'] ?: '',
        $estado,
        $r['motivo_revision'] ?: '',
        $promedio,
    ];

    // Una celda por pregunta
    foreach ($preguntas as $p) {
        $pid = (int) $p['id'];
        $v   = $vals[$pid] ?? null;

        if (!$v) {
            $fila[] = '';
            continue;
        }

        if ($p['tipo'] === 'escala' && $v['valor_escala'] !== null) {
            $fila[] = (int) $v['valor_escala'];
        } elseif ($v['valor_texto'] !== null) {
            // Limpiar el texto: quitar saltos de línea para que el CSV quede bien
            $texto = preg_replace('/\s+/', ' ', $v['valor_texto']);
            $fila[] = $texto;
        } else {
            $fila[] = '';
        }
    }

    // IP al final
    $fila[] = $r['ip'] ?: '';

    fputcsv($out, $fila, ';');
}

fclose($out);
exit;
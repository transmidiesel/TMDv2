<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: encuesta-preguntas.php');
    exit;
}

$id               = (int)($_POST['id'] ?? 0);
$texto            = trim($_POST['texto'] ?? '');
$tipo             = $_POST['tipo'] ?? 'texto';
$orden            = (int)($_POST['orden'] ?? 0);
$obligatoria      = isset($_POST['obligatoria']) ? 1 : 0;
$activa           = isset($_POST['activa']) ? 1 : 0;

// Validaciones básicas
if ($texto === '') {
    header('Location: encuesta-pregunta-form.php?error=El texto de la pregunta es obligatorio' . ($id ? "&id=$id" : ''));
    exit;
}
if (!in_array($tipo, ['texto', 'escala', 'opcion'], true)) {
    header('Location: encuesta-pregunta-form.php?error=Tipo de pregunta inválido' . ($id ? "&id=$id" : ''));
    exit;
}

// Valores específicos por tipo
$escalaMin      = null;
$escalaMax      = null;
$escalaLabelMin = null;
$escalaLabelMax = null;
$opciones       = null;

if ($tipo === 'escala') {
    $escalaMin      = (int)($_POST['escala_min'] ?? 1);
    $escalaMax      = (int)($_POST['escala_max'] ?? 10);
    $escalaLabelMin = trim($_POST['escala_label_min'] ?? '');
    $escalaLabelMax = trim($_POST['escala_label_max'] ?? '');

    if ($escalaMin >= $escalaMax) {
        header('Location: encuesta-pregunta-form.php?error=El mínimo debe ser menor que el máximo' . ($id ? "&id=$id" : ''));
        exit;
    }
    if ($escalaLabelMin === '') $escalaLabelMin = null;
    if ($escalaLabelMax === '') $escalaLabelMax = null;
}

if ($tipo === 'opcion') {
    $rawOpciones = trim($_POST['opciones'] ?? '');
    if ($rawOpciones === '') {
        header('Location: encuesta-pregunta-form.php?error=Debes escribir al menos una opción' . ($id ? "&id=$id" : ''));
        exit;
    }
    // Convertir líneas a array y limpiar
    $lineas = preg_split('/\r\n|\r|\n/', $rawOpciones);
    $lineas = array_values(array_filter(array_map('trim', $lineas), fn($l) => $l !== ''));
    if (count($lineas) < 2) {
        header('Location: encuesta-pregunta-form.php?error=Debes escribir al menos 2 opciones' . ($id ? "&id=$id" : ''));
        exit;
    }
    $opciones = json_encode($lineas, JSON_UNESCAPED_UNICODE);
}

// ---------- Insertar o actualizar ----------
try {
    if ($id > 0) {
        $stmt = $pdo->prepare("
            UPDATE encuesta_preguntas
            SET texto = :texto,
                tipo = :tipo,
                orden = :orden,
                escala_min = :escala_min,
                escala_max = :escala_max,
                escala_label_min = :elmin,
                escala_label_max = :elmax,
                opciones = :opciones,
                obligatoria = :obligatoria,
                activa = :activa
            WHERE id = :id
        ");
        $stmt->execute([
            ':texto'       => $texto,
            ':tipo'        => $tipo,
            ':orden'       => $orden,
            ':escala_min'  => $escalaMin,
            ':escala_max'  => $escalaMax,
            ':elmin'       => $escalaLabelMin,
            ':elmax'       => $escalaLabelMax,
            ':opciones'    => $opciones,
            ':obligatoria' => $obligatoria,
            ':activa'      => $activa,
            ':id'          => $id,
        ]);
        header('Location: encuesta-preguntas.php?ok=editada');
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO encuesta_preguntas
                (texto, tipo, orden, escala_min, escala_max, escala_label_min, escala_label_max,
                 opciones, obligatoria, activa)
            VALUES
                (:texto, :tipo, :orden, :escala_min, :escala_max, :elmin, :elmax,
                 :opciones, :obligatoria, :activa)
        ");
        $stmt->execute([
            ':texto'       => $texto,
            ':tipo'        => $tipo,
            ':orden'       => $orden,
            ':escala_min'  => $escalaMin,
            ':escala_max'  => $escalaMax,
            ':elmin'       => $escalaLabelMin,
            ':elmax'       => $escalaLabelMax,
            ':opciones'    => $opciones,
            ':obligatoria' => $obligatoria,
            ':activa'      => $activa,
        ]);
        header('Location: encuesta-preguntas.php?ok=creada');
    }
    exit;

} catch (Exception $e) {
    header('Location: encuesta-preguntas.php?error=' . urlencode($e->getMessage()));
    exit;
}
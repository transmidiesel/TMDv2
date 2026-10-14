<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/db.php';

$mensaje = '';
$tipo    = '';

if (isset($_GET['ok'])) {
    $mensaje = match($_GET['ok']) {
        'creada'      => 'Pregunta creada correctamente.',
        'editada'     => 'Pregunta actualizada correctamente.',
        'eliminada'   => 'Pregunta eliminada.',
        'activada'    => 'Pregunta activada.',
        'desactivada' => 'Pregunta desactivada.',
        default       => ''
    };
    $tipo = 'success';
}
if (isset($_GET['error'])) {
    $mensaje = 'Ocurrió un error: ' . htmlspecialchars($_GET['error']);
    $tipo = 'error';
}

// Listado de preguntas
$preguntas = $pdo->query("
    SELECT id, orden, texto, tipo, escala_min, escala_max, escala_label_min, escala_label_max,
           opciones, obligatoria, activa
    FROM encuesta_preguntas
    ORDER BY orden ASC, id ASC
")->fetchAll(PDO::FETCH_ASSOC);

// Cuántas respuestas usan cada pregunta
$usosPorPregunta = [];
$stmtU = $pdo->query("
    SELECT pregunta_id, COUNT(*) AS usos
    FROM encuesta_valores
    GROUP BY pregunta_id
");
while ($row = $stmtU->fetch(PDO::FETCH_ASSOC)) {
    $usosPorPregunta[(int)$row['pregunta_id']] = (int)$row['usos'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Preguntas de Encuesta · Panel Transmidiesel</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="admin.css">
</head>
<body>

<?php include 'partials/sidebar.php'; ?>

<main class="admin-main">
  <header class="admin-topbar">
    <h1>Preguntas de Encuesta</h1>
    <a href="logout.php" class="btn-logout">Cerrar sesión</a>
  </header>

  <section class="admin-content">

    <?php if ($mensaje): ?>
      <div class="alert alert-<?= $tipo ?>"><?= $mensaje ?></div>
    <?php endif; ?>

    <div class="page-head">
      <div>
        <h2>Configuración de la encuesta</h2>
        <p class="sub">
          <?= count($preguntas) ?> pregunta(s) configurada(s) · Ordena las preguntas con el número <strong>Orden</strong>
        </p>
      </div>
      <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <a href="encuestas.php" class="btn btn-secondary">← Ver encuestas</a>
        <a href="encuesta-pregunta-form.php" class="btn btn-primary">+ Nueva pregunta</a>
      </div>
    </div>

    <div class="info-box" style="margin-bottom:24px;">
      <h3>💡 ¿Cómo funciona?</h3>
      <ul>
        <li><strong>Tipo texto:</strong> el cliente escribe libremente (nombre, documento, comentario, etc.)</li>
        <li><strong>Tipo escala:</strong> el cliente califica con un número (ej: 1-10)</li>
        <li><strong>Tipo opción:</strong> el cliente elige entre varias opciones predefinidas</li>
        <li><strong>Orden:</strong> números más bajos = aparecen primero en la encuesta</li>
        <li><strong>Obligatoria:</strong> si está marcada, el cliente debe responderla para poder enviar</li>
        <li><strong>Activa:</strong> si está desactivada, la pregunta no se muestra ni cuenta en el promedio</li>
      </ul>
      <p style="color:var(--text-muted);font-size:13px;margin-top:12px;">
        ⚠ Las preguntas no se pueden eliminar si ya tienen respuestas guardadas. Desactívalas en su lugar.
      </p>
    </div>

    <?php if (empty($preguntas)): ?>
      <div class="table-wrap">
        <div class="empty-state">
          <div class="icon">❓</div>
          <h3>No hay preguntas todavía</h3>
          <p>Crea la primera con el botón "+ Nueva pregunta".</p>
        </div>
      </div>
    <?php else: ?>
      <div class="table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th style="width:60px;">Orden</th>
              <th style="width:110px;">Tipo</th>
              <th>Pregunta</th>
              <th class="col-rango" style="width:90px;">Rango</th>
              <th class="col-obligatoria" style="width:100px;">Obligatoria</th>
              <th style="width:100px;">Estado</th>
              <th class="col-respuestas" style="width:100px;">Respuestas</th>
              <th style="width:190px;text-align:right;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($preguntas as $p):
              $tipoBadge = match($p['tipo']) {
                  'texto'  => '📝 Texto',
                  'escala' => '⭐ Escala',
                  'opcion' => '📋 Opción',
                  default  => $p['tipo']
              };
              $rango = ($p['tipo'] === 'escala' && $p['escala_min'] !== null && $p['escala_max'] !== null)
                  ? "{$p['escala_min']} - {$p['escala_max']}"
                  : '—';
              $usos = $usosPorPregunta[(int)$p['id']] ?? 0;
              $tieneRespuestas = $usos > 0;
            ?>
              <tr>
                <td><span class="badge badge-order"><?= (int)$p['orden'] ?></span></td>
                <td><span class="encuesta-tipo-badge"><?= $tipoBadge ?></span></td>
                <td class="td-title">
                  <?= htmlspecialchars($p['texto']) ?>
                  <?php if ($p['tipo'] === 'escala' && ($p['escala_label_min'] || $p['escala_label_max'])): ?>
                    <small>"<?= htmlspecialchars($p['escala_label_min']) ?>" → "<?= htmlspecialchars($p['escala_label_max']) ?>"</small>
                  <?php endif; ?>
                </td>
                <td class="col-rango"><?= $rango ?></td>
                <td class="col-obligatoria">
                  <?php if ($p['obligatoria']): ?>
                    <span class="badge badge-active">Sí</span>
                  <?php else: ?>
                    <span class="badge badge-inactive">No</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($p['activa']): ?>
                    <span class="badge badge-active">Activa</span>
                  <?php else: ?>
                    <span class="badge badge-inactive">Inactiva</span>
                  <?php endif; ?>
                </td>
                <td class="col-respuestas"><span class="badge badge-order"><?= $usos ?></span></td>
                <td>
                  <div class="td-actions">
                    <a href="encuesta-pregunta-form.php?id=<?= $p['id'] ?>" class="btn btn-secondary btn-sm">Editar</a>
                    <?php if ($tieneRespuestas): ?>
                      <a href="encuesta-pregunta-eliminar.php?id=<?= $p['id'] ?>"
                         class="btn btn-danger btn-sm"
                         onclick="return confirm('Esta pregunta ya tiene <?= $usos ?> respuesta(s) guardada(s).\n¿Estás SEGURO de eliminarla?\n\nSe eliminarán TODAS las respuestas asociadas.');"
                         title="Tiene respuestas guardadas">
                         Eliminar
                      </a>
                    <?php else: ?>
                      <a href="encuesta-pregunta-eliminar.php?id=<?= $p['id'] ?>"
                         class="btn btn-danger btn-sm"
                         onclick="return confirm('¿Eliminar esta pregunta?\n\n<?= htmlspecialchars(addslashes($p['texto'])) ?>');">
                         Eliminar
                      </a>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>

  </section>
</main>

</body>
</html>
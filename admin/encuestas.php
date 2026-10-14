<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/db.php';

$mensaje = '';
$tipo    = '';

if (isset($_GET['ok'])) {
    $mensaje = match($_GET['ok']) {
        'aprobada'  => 'Encuesta aprobada y publicada en el sitio.',
        'rechazada' => 'Encuesta marcada como no publicable.',
        'eliminada' => 'Encuesta eliminada permanentemente.',
        default     => ''
    };
    $tipo = 'success';
}
if (isset($_GET['error'])) {
    $mensaje = 'Ocurrió un error: ' . htmlspecialchars($_GET['error']);
    $tipo = 'error';
}

// ---------- Filtro por estado ----------
$filtro = $_GET['filtro'] ?? 'todas';
$where  = '';
if ($filtro === 'aprobadas')  $where = 'WHERE aprobada = 1';
if ($filtro === 'pendientes') $where = 'WHERE aprobada = 0';

// ---------- Contadores ----------
$totalTodas      = (int) $pdo->query("SELECT COUNT(*) FROM encuesta_respuestas")->fetchColumn();
$totalAprobadas  = (int) $pdo->query("SELECT COUNT(*) FROM encuesta_respuestas WHERE aprobada = 1")->fetchColumn();
$totalPendientes = (int) $pdo->query("SELECT COUNT(*) FROM encuesta_respuestas WHERE aprobada = 0")->fetchColumn();

// ---------- Listado ----------
$stmt = $pdo->query("
    SELECT
        r.id,
        r.nombre,
        r.documento,
        r.aprobada,
        r.motivo_revision,
        r.fecha,
        (
            SELECT ROUND(AVG(v.valor_escala), 1)
            FROM encuesta_valores v
            INNER JOIN encuesta_preguntas p ON p.id = v.pregunta_id
            WHERE v.respuesta_id = r.id AND p.tipo = 'escala' AND v.valor_escala IS NOT NULL
        ) AS promedio
    FROM encuesta_respuestas r
    $where
    ORDER BY r.fecha DESC
");
$encuestas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ---------- Obtener el comentario de cada encuesta ----------
// (buscando el primer valor de texto que contenga palabras clave)
$comentariosPorId = [];
if (!empty($encuestas)) {
    $ids = array_column($encuestas, 'id');
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $stmtC = $pdo->prepare("
        SELECT v.respuesta_id, v.valor_texto, p.texto
        FROM encuesta_valores v
        INNER JOIN encuesta_preguntas p ON p.id = v.pregunta_id
        WHERE v.respuesta_id IN ($placeholders)
          AND p.tipo = 'texto'
          AND v.valor_texto IS NOT NULL
          AND v.valor_texto != ''
    ");
    $stmtC->execute($ids);
    while ($row = $stmtC->fetch(PDO::FETCH_ASSOC)) {
        $rid = (int) $row['respuesta_id'];
        if (preg_match('/expectativa|mejora|comentario|observaci|sugerencia/i', $row['texto'])) {
            if (!isset($comentariosPorId[$rid])) {
                $comentariosPorId[$rid] = $row['valor_texto'];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Encuestas · Panel Transmidiesel</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="admin.css">
</head>
<body>

<?php include 'partials/sidebar.php'; ?>

<main class="admin-main">
  <header class="admin-topbar">
    <h1>Encuestas de Satisfacción</h1>
    <a href="logout.php" class="btn-logout">Cerrar sesión</a>
  </header>

  <section class="admin-content">

    <?php if ($mensaje): ?>
      <div class="alert alert-<?= $tipo ?>"><?= $mensaje ?></div>
    <?php endif; ?>

    <div class="page-head">
      <div>
        <h2>Encuestas recibidas</h2>
        <p class="sub">
          <?= $totalTodas ?> en total ·
          <?= $totalAprobadas ?> aprobada(s) ·
          <?= $totalPendientes ?> pendiente(s) de revisión
        </p>
      </div>
      <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <a href="encuesta-exportar.php?filtro=<?= $filtro === 'pendientes' ? 'pendientes' : ($filtro === 'aprobadas' ? 'aprobadas' : 'todas') ?>" 
   class="btn btn-secondary"
   title="Exporta solo las encuestas que se están mostrando">
  ⬇ Descargar CSV<?= $filtro !== 'todas' ? ' (' . $filtro . ')' : '' ?>
</a>
        <a href="encuesta-preguntas.php" class="btn btn-primary">
          ⚙ Gestionar preguntas
        </a>
      </div>
    </div>

    <!-- Filtros -->
    <div class="encuesta-filtros">
      <a href="?filtro=todas" class="<?= $filtro === 'todas' ? 'active' : '' ?>">
        Todas <span class="count"><?= $totalTodas ?></span>
      </a>
      <a href="?filtro=aprobadas" class="<?= $filtro === 'aprobadas' ? 'active' : '' ?>">
        Aprobadas <span class="count"><?= $totalAprobadas ?></span>
      </a>
      <a href="?filtro=pendientes" class="<?= $filtro === 'pendientes' ? 'active' : '' ?>">
        Pendientes <span class="count"><?= $totalPendientes ?></span>
      </a>
    </div>

    <?php if (empty($encuestas)): ?>
      <div class="table-wrap">
        <div class="empty-state">
          <div class="icon">💬</div>
          <h3>No hay encuestas <?= $filtro !== 'todas' ? $filtro : 'registradas' ?></h3>
          <p>Cuando alguien responda la encuesta, aparecerá aquí.</p>
        </div>
      </div>
    <?php else: ?>
      <div class="table-wrap">
        <table class="admin-table encuesta-table">
          <thead>
            <tr>
              <th style="width:70px;">ID</th>
              <th>Cliente</th>
              <th style="width:110px;">Puntaje</th>
              <th>Comentario</th>
              <th style="width:130px;">Fecha</th>
              <th style="width:120px;">Estado</th>
              <th style="width:230px;text-align:right;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($encuestas as $e):
              $promedio = $e['promedio'] !== null ? (float) $e['promedio'] : 0;
              $comentario = $comentariosPorId[$e['id']] ?? '';
              $comentarioCorto = mb_strimwidth($comentario, 0, 100, '…', 'UTF-8');
              $fechaTxt = date('d M Y · H:i', strtotime($e['fecha']));
            ?>
              <tr>
                <td><span class="badge badge-order">#<?= (int)$e['id'] ?></span></td>
                <td class="td-title">
                  <?= htmlspecialchars($e['nombre']) ?>
                  <?php if ($e['documento']): ?>
                    <small>Doc: <?= htmlspecialchars($e['documento']) ?></small>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($e['promedio'] !== null): ?>
                    <span class="encuesta-puntaje <?= $promedio >= 9 ? 'alta' : ($promedio >= 7 ? 'media' : 'baja') ?>">
                      <?= number_format($promedio, 1) ?>/10
                    </span>
                  <?php else: ?>
                    <span style="color:#999;font-size:12px;">—</span>
                  <?php endif; ?>
                </td>
                <td class="td-comment">
                  <?= $comentarioCorto ? htmlspecialchars($comentarioCorto) : '<span style="color:#999;font-style:italic;">Sin comentario</span>' ?>
                </td>
                <td><small style="color:var(--text-muted);"><?= $fechaTxt ?></small></td>
                <td>
                  <?php if ($e['aprobada']): ?>
                    <span class="badge badge-active">Publicada</span>
                  <?php else: ?>
                    <span class="badge badge-pending" title="<?= htmlspecialchars($e['motivo_revision'] ?? '') ?>">Pendiente</span>
                  <?php endif; ?>
                </td>
                <td>
                  <div class="td-actions">
                    <?php if (!$e['aprobada']): ?>
                      <a href="encuesta-aprobar.php?id=<?= $e['id'] ?>&accion=aprobar"
                         class="btn btn-primary btn-sm"
                         onclick="return confirm('¿Aprobar y publicar esta encuesta en el sitio?');">
                         Aprobar
                      </a>
                    <?php else: ?>
                      <a href="encuesta-aprobar.php?id=<?= $e['id'] ?>&accion=rechazar"
                         class="btn btn-secondary btn-sm"
                         onclick="return confirm('¿Ocultar esta encuesta del sitio público?');">
                         Ocultar
                      </a>
                    <?php endif; ?>
                    <a href="encuesta-eliminar.php?id=<?= $e['id'] ?>"
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('¿Eliminar esta encuesta permanentemente?\n\nEsta acción no se puede deshacer.');">
                       Eliminar
                    </a>
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
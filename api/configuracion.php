<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
require_once __DIR__ . '/../includes/db.php';

try {
    $stmt = $pdo->query("SELECT clave, valor FROM configuracion");
    $config = [];
    foreach ($stmt->fetchAll() as $row) {
        $config[$row['clave']] = $row['valor'];
    }
    echo json_encode($config);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
<!-- Al final del <form> en admin/configuracion.php, dentro de <section class="admin-content"> -->

<?php
// Cargar TRM actual
$trm = [];
foreach ($pdo->query("SELECT clave, valor FROM configuracion WHERE clave LIKE 'trm_%'") as $row) {
    $trm[$row['clave']] = $row['valor'];
}
?>

<div class="form-card" style="margin-top:32px;">
  <h3 style="font-family:'Space Grotesk';margin:0 0 16px;color:var(--accent);">💱 Tasas de cambio (TRM)</h3>
  <p style="color:var(--text-muted);font-size:14px;margin:0 0 18px;">
    Estas tasas se actualizan automáticamente <strong>una vez al día</strong> por el cron job. Los precios del sitio público se calculan con estos valores.
  </p>

  <table class="admin-table" style="margin-bottom:16px;">
    <thead>
      <tr>
        <th>Moneda</th>
        <th>Tasa actual</th>
      </tr>
    </thead>
    <tbody>
      <tr><td><strong>COP</strong> (Peso colombiano)</td><td><?= number_format((float)($trm['trm_cop'] ?? 0), 2) ?> por 1 USD</td></tr>
      <tr><td><strong>EUR</strong> (Euro)</td><td><?= number_format((float)($trm['trm_eur'] ?? 0), 4) ?> por 1 USD</td></tr>
      <tr><td><strong>MXN</strong> (Peso mexicano)</td><td><?= number_format((float)($trm['trm_mxn'] ?? 0), 4) ?> por 1 USD</td></tr>
    </tbody>
  </table>

  <p style="font-size:13px;color:var(--text-muted);">
    <strong>Última actualización:</strong>
    <?= htmlspecialchars($trm['trm_actualizada'] ?? 'Nunca') ?>
  </p>

  <a href="../api/actualizar-trm.php?token=TMD_CRON_2026_SECRETO_CAMBIAME"
     target="_blank"
     class="btn btn-secondary"
     onclick="return confirm('¿Forzar actualización manual de TRM?');">
    🔄 Forzar actualización ahora
  </a>
</div>
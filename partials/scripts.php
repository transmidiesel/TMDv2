<?php
// =========================================================
// partials/scripts.php
// Scripts comunes al final del <body>.
// Requiere: $base (ruta relativa a la raíz del proyecto)
// Opcional: $extraScripts (array de rutas adicionales)
// =========================================================

$base         = $base ?? './';
$extraScripts = $extraScripts ?? [];
?>
<script src="<?= $base ?>script.js?v=5"></script>
<script src="<?= $base ?>footer-certs.js" defer></script>
<?php foreach ($extraScripts as $s): ?>
<script src="<?= $base . htmlspecialchars($s) ?>" defer></script>
<?php endforeach; ?>
</body>
</html>
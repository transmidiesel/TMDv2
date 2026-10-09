<?php
// =========================================================
// partials/head.php
// <head> común a todas las páginas.
// Requiere: $base (ruta relativa a la raíz del proyecto)
// Opcional: $pageTitle (título de la página)
// =========================================================

$base      = $base ?? './';
$pageTitle = $pageTitle ?? 'Transmidiesel S.A.S';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= $base ?>styles.css">
<link rel="icon" type="image/png" sizes="32x32" href="<?= $base ?>logos/favicon_io/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="<?= $base ?>logos/favicon_io/favicon-16x16.png">
<link rel="apple-touch-icon" sizes="180x180" href="<?= $base ?>logos/favicon_io/apple-touch-icon.png">
<link rel="manifest" href="<?= $base ?>logos/favicon_io/site.webmanifest">
</head>
<body>

<div class="page-transition" id="pageTransition"></div>
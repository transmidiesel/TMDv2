<?php
require 'db.php';

echo "<h1>✅ Conexión exitosa a la base de datos</h1>";

$tablas = ['noticias', 'marcas', 'certificaciones', 'configuracion', 'usuarios'];

foreach ($tablas as $t) {
    $count = $pdo->query("SELECT COUNT(*) FROM $t")->fetchColumn();
    echo "<p><strong>$t:</strong> $count registros</p>";
}
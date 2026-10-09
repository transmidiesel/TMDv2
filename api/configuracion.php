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
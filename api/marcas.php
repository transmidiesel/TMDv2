<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
require_once __DIR__ . '/../includes/db.php';

try {
    $stmt = $pdo->query("SELECT * FROM marcas WHERE activa = 1 ORDER BY orden ASC, id ASC");
    echo json_encode($stmt->fetchAll());
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
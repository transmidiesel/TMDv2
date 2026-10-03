<?php
// =========================================================
// API de Noticias — devuelve JSON
// GET api/noticias.php            → lista todas las activas
// GET api/noticias.php?id=3       → devuelve una
// =========================================================

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../includes/db.php';

try {
    if (isset($_GET['id'])) {
        $stmt = $pdo->prepare("SELECT * FROM noticias WHERE id = ? AND activa = 1");
        $stmt->execute([(int)$_GET['id']]);
        $noticia = $stmt->fetch();
        echo json_encode($noticia ?: ['error' => 'No encontrada']);
    } else {
        $stmt = $pdo->query("SELECT * FROM noticias WHERE activa = 1 ORDER BY orden ASC, id ASC");
        echo json_encode($stmt->fetchAll());
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
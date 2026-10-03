<?php
// =========================================================
// Conexión a la base de datos — CONFIGURACIÓN LOCAL (XAMPP)
// =========================================================

$host    = 'localhost';
$db      = 'transmidiesel_db';
$user    = 'root';
$pass    = '';           // En XAMPP, root no tiene contraseña por defecto
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode(['error' => 'Error de conexión: ' . $e->getMessage()]));
}
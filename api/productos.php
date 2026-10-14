<?php
// =========================================================
// api/productos.php
// Devuelve productos + tasas de cambio guardadas en BD
// NO llama a ninguna API externa — todo viene de tu BD
// =========================================================
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../includes/db.php';

try {
    // ---------- 1) Productos activos ----------
    $stmt = $pdo->query("
        SELECT id, nombre, descripcion, imagen, precio_usd, categoria, orden
        FROM productos
        WHERE activo = 1
        ORDER BY orden ASC, id ASC
    ");
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Asegurar tipos correctos y normalizar la ruta de la imagen
    foreach ($productos as &$p) {
        $p['id']         = (int)$p['id'];
        $p['precio_usd'] = (float)$p['precio_usd'];
        $p['orden']      = (int)$p['orden'];

        if (!empty($p['imagen'])) {
            $img = $p['imagen'];
            if (!preg_match('#^https?://#i', $img)) {
                $img = ltrim($img, './');
                $img = ltrim($img, '/');
                $p['imagen'] = $img;
            }
        }
    }
    unset($p);

    // ---------- 2) Tasas de cambio desde configuracion ----------
    // Se leen TODAS las filas y se filtran en PHP para tolerar
    // mayúsculas/minúsculas (trm_cop, TRM_COP, TrM_CoP, etc.)
    $stmt = $pdo->query("SELECT clave, valor FROM configuracion");
    $tasas = [];
    $fechaActualizacion = null;

    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $clave = strtolower(trim($row['clave']));

        if ($clave === 'trm_actualizada') {
            $fechaActualizacion = $row['valor'];
            continue;
        }

        if (strpos($clave, 'trm_') === 0) {
            $moneda = strtoupper(substr($clave, 4));   // "trm_cop" -> "COP"
            $tasas[$moneda] = (float)$row['valor'];
        }
    }

    // ---------- 3) base_url dinámica ----------
    $scriptPath = $_SERVER['SCRIPT_NAME'] ?? '/api/productos.php';
    $apiFolder  = dirname($scriptPath);
    $baseUrl    = rtrim(dirname($apiFolder), '/') . '/';

    // ---------- 4) Respuesta ----------
    echo json_encode([
        'productos'         => $productos,
        'tasas'             => $tasas,
        'fecha_actualizada' => $fechaActualizacion,
        'moneda_base'       => 'USD',
        'base_url'          => $baseUrl
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error'   => 'Error al cargar productos',
        'detalle' => $e->getMessage()
    ]);
}
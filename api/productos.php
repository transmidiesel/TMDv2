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

        // Normalizar la ruta de la imagen: siempre dejarla como ruta relativa a la raíz
        //   "uploads/productos/xxx.jpg"    → "uploads/productos/xxx.jpg"
        //   "productos/box-cooler.png"     → "productos/box-cooler.png"
        //   "../uploads/productos/xxx.jpg" → "uploads/productos/xxx.jpg"
        //   "http://..."                    → se deja igual
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
    $stmt = $pdo->query("
        SELECT clave, valor FROM configuracion
        WHERE clave IN ('trm_cop','trm_eur','trm_mxn','trm_actualizada')
    ");
    $tasas = [];
    $fechaActualizacion = null;

    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if ($row['clave'] === 'trm_actualizada') {
            $fechaActualizacion = $row['valor'];
        } else {
            $moneda = strtoupper(str_replace('trm_', '', $row['clave']));
            $tasas[$moneda] = (float)$row['valor'];
        }
    }

    // ---------- 3) Calcular base_url dinámica (funciona en local y en producción) ----------
    // Ejemplo local:     /transmidiesel/api/productos.php  → base_url = "/transmidiesel/"
    // Ejemplo prod:      /api/productos.php                 → base_url = "/"
    $scriptPath = $_SERVER['SCRIPT_NAME'] ?? '/api/productos.php';
    $apiFolder  = dirname($scriptPath);                   // "/transmidiesel/api"  o  "/api"
    $baseUrl    = rtrim(dirname($apiFolder), '/') . '/';  // "/transmidiesel/"      o  "/"

    // ---------- 4) Respuesta ----------
    echo json_encode([
        'productos'         => $productos,
        'tasas'             => $tasas,
        'fecha_actualizada' => $fechaActualizacion,
        'moneda_base'       => 'USD',
        'base_url'          => $baseUrl
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al cargar productos']);
}
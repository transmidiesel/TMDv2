<?php
// =========================================================
// api/actualizar-trm.php
// Se ejecuta UNA VEZ AL DÍA desde cron job
// Las credenciales se leen desde el archivo .env
// =========================================================

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/env.php';

// ---------- Seguridad: token desde .env ----------
$tokenEsperado = env('CRON_TOKEN', '');
$esCLI         = php_sapi_name() === 'cli';
$tokenGet      = $_GET['token'] ?? '';

if (!$esCLI) {
    if ($tokenEsperado === '' || $tokenGet !== $tokenEsperado) {
        http_response_code(403);
        exit('Acceso denegado');
    }
}

// ---------- Configuración desde .env ----------
$apiKey     = env('CURRENCY_API_KEY', '');
$monedaBase = env('MONEDA_BASE', 'USD');
$monedas    = array_filter(array_map('trim', explode(',', env('MONEDAS', 'COP,EUR,MXN'))));

if ($apiKey === '') {
    $msg = 'Falta CURRENCY_API_KEY en el archivo .env';
    if ($esCLI) {
        fwrite(STDERR, "❌ $msg\n");
        exit(1);
    }
    http_response_code(500);
    header('Content-Type: application/json');
    exit(json_encode(['success' => false, 'error' => $msg]));
}

$log = [];

try {
    // ---------- 1) Llamar a currencyapi ----------
    $url = "https://api.currencyapi.com/v3/latest?apikey=" . urlencode($apiKey)
         . "&base_currency=" . urlencode($monedaBase)
         . "&currencies=" . urlencode(implode(',', $monedas));

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);
    $respuesta = curl_exec($ch);
    $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) throw new Exception("cURL: $curlError");
    if ($httpCode === 401) throw new Exception("API key inválida (HTTP 401). Revisa CURRENCY_API_KEY en .env");
    if ($httpCode === 429) throw new Exception("Límite de la API excedido (HTTP 429)");
    if ($httpCode !== 200) throw new Exception("HTTP $httpCode al llamar a currencyapi");

    $data = json_decode($respuesta, true);
    if (!isset($data['data'])) throw new Exception("Respuesta sin campo 'data'");

    // ---------- 2) Guardar cada moneda (INSERT ... ON DUPLICATE KEY UPDATE) ----------
    // Este método es atómico: si la clave existe → UPDATE; si no existe → INSERT.
    // Evita el bug de rowCount() === 0 cuando el valor es idéntico.
    $stmt = $pdo->prepare("
        INSERT INTO configuracion (clave, valor, descripcion)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE valor = VALUES(valor)
    ");

    foreach ($monedas as $moneda) {
        if (isset($data['data'][$moneda]['value'])) {
            $valor = (float)$data['data'][$moneda]['value'];
            $clave = 'trm_' . strtolower($moneda);

            $stmt->execute([
                $clave,
                number_format($valor, 6, '.', ''),
                "Tasa $moneda por 1 $monedaBase"
            ]);

            $log[] = "$moneda: " . number_format($valor, 4);
        }
    }

    // ---------- 3) Fecha de actualización (mismo método) ----------
    $pdo->prepare("
        INSERT INTO configuracion (clave, valor, descripcion)
        VALUES ('trm_actualizada', ?, 'Fecha de última actualización TRM')
        ON DUPLICATE KEY UPDATE valor = VALUES(valor)
    ")->execute([date('Y-m-d H:i:s')]);

    // ---------- 4) Log ----------
    @file_put_contents(
        __DIR__ . '/../logs/trm.log',
        date('[Y-m-d H:i:s]') . " OK - " . implode(' | ', $log) . "\n",
        FILE_APPEND
    );

    // ---------- 5) Respuesta ----------
    if ($esCLI) {
        echo "✅ TRM actualizada:\n";
        foreach ($log as $l) echo "   - $l\n";
    } else {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'tasas'   => $log,
            'fecha'   => date('Y-m-d H:i:s')
        ], JSON_UNESCAPED_UNICODE);
    }

} catch (Exception $e) {
    @file_put_contents(
        __DIR__ . '/../logs/trm.log',
        date('[Y-m-d H:i:s]') . " ERROR: " . $e->getMessage() . "\n",
        FILE_APPEND
    );

    if ($esCLI) {
        fwrite(STDERR, "❌ " . $e->getMessage() . "\n");
        exit(1);
    } else {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
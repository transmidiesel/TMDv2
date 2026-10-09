<?php
// =========================================================
// includes/config_loader.php
// Carga toda la configuración desde la BD una sola vez.
// Todos los partials (navbar, footer, etc.) la usan.
// =========================================================

require_once __DIR__ . '/db.php';

if (!function_exists('cargarConfiguracion')) {
    function cargarConfiguracion($pdo) {
        static $config = null;
        if ($config !== null) return $config;

        $config = [];
        try {
            $stmt = $pdo->query("SELECT clave, valor FROM configuracion");
            while ($row = $stmt->fetch()) {
                $config[$row['clave']] = $row['valor'];
            }
        } catch (Exception $e) {
            // Silencioso: si falla, dejamos el array vacío
        }

        return $config;
    }
}

// Helper para obtener un valor con fallback
if (!function_exists('cfg')) {
    function cfg($clave, $default = '') {
        global $CONFIG_TRANSMIDIESEL;
        return $CONFIG_TRANSMIDIESEL[$clave] ?? $default;
    }
}

// Helper para construir URLs absolutas al proyecto
if (!function_exists('url')) {
    function url($ruta = '') {
        global $BASE_URL;
        return ($BASE_URL ?? '/transmiweb/') . ltrim($ruta, '/');
    }
}

// Cargar la configuración de una vez
$CONFIG_TRANSMIDIESEL = cargarConfiguracion($pdo);
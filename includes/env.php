<?php
// =========================================================
// includes/env.php
// Carga las variables de entorno desde el archivo .env
// Uso: env('CURRENCY_API_KEY', 'valor_por_defecto')
// =========================================================

function cargarEnv(): array
{
    static $vars = null;
    if ($vars !== null) return $vars;

    $vars = [];
    $rutaEnv = __DIR__ . '/../.env';

    if (!file_exists($rutaEnv)) {
        return $vars;
    }

    $lineas = file($rutaEnv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lineas as $linea) {
        $linea = trim($linea);
        if ($linea === '' || $linea[0] === '#') continue;
        if (strpos($linea, '=') === false) continue;

        [$clave, $valor] = explode('=', $linea, 2);
        $clave = trim($clave);
        $valor = trim($valor);

        // Quitar comillas si las tiene
        if (strlen($valor) >= 2) {
            $primero = $valor[0];
            $ultimo  = $valor[strlen($valor) - 1];
            if (($primero === '"' && $ultimo === '"') || ($primero === "'" && $ultimo === "'")) {
                $valor = substr($valor, 1, -1);
            }
        }

        $vars[$clave] = $valor;
    }

    return $vars;
}

function env(string $clave, $porDefecto = null)
{
    $vars = cargarEnv();
    return $vars[$clave] ?? $porDefecto;
}
<?php
/**
 * Script de Diagnóstico para problemas de Login
 */

echo "<!DOCTYPE html><html><head><title>Diagnóstico</title>";
echo "<style>body{font-family:monospace;background:#f5f5f5;padding:20px;} ";
echo ".diagnostic{margin:20px 0;padding:15px;border-left:4px solid #ccc;background:#fff;}";
echo ".pass{border-color:#4CAF50;background:#e8f5e9;}";
echo ".fail{border-color:#f44336;background:#ffebee;}";
echo ".warn{border-color:#ff9800;background:#fff3e0;}";
echo "h2{color:#333;margin:0;font-size:16px;}";
echo "p{margin:5px 0;font-size:14px;}";
echo "code{background:#eee;padding:2px 5px;border-radius:3px;}";
echo "</style></head><body>";

echo "<h1>🔍 Diagnóstico de Login en Servidor</h1>";

// 1. Verificar PHP version
$diagnostic = '<div class="diagnostic pass"><h2>✓ PHP Version</h2>';
$diagnostic .= '<p>PHP ' . phpversion() . '</p>';
$diagnostic .= '</div>';
echo $diagnostic;

// 2. Verificar soporte para sesiones
$diagnostic = '<div class="diagnostic ' . (function_exists('session_start') ? 'pass' : 'fail') . '">';
$diagnostic .= '<h2>' . (function_exists('session_start') ? '✓' : '✗') . ' Soporte de Sesiones</h2>';
$diagnostic .= '<p>session_start(): ' . (function_exists('session_start') ? 'DISPONIBLE' : 'NO DISPONIBLE') . '</p>';
$diagnostic .= '</div>';
echo $diagnostic;

// 3. Verificar directorio de sesiones
$session_path = session_save_path();
$diagnostic = '<div class="diagnostic ' . (is_writable($session_path ?: '/tmp') ? 'pass' : 'fail') . '">';
$diagnostic .= '<h2>' . (is_writable($session_path ?: '/tmp') ? '✓' : '✗') . ' Directorio de Sesiones</h2>';
$diagnostic .= '<p>Ruta: <code>' . ($session_path ?: 'default (/tmp)') . '</code></p>';
$diagnostic .= '<p>Escribible: ' . (is_writable($session_path ?: '/tmp') ? 'SÍ' : 'NO') . '</p>';
$diagnostic .= '</div>';
echo $diagnostic;

// 4. Verificar bcrypt para contraseñas
$diagnostic = '<div class="diagnostic ' . (function_exists('password_hash') ? 'pass' : 'fail') . '">';
$diagnostic .= '<h2>' . (function_exists('password_hash') ? '✓' : '✗') . ' Soporte Bcrypt</h2>';
$diagnostic .= '<p>password_hash(): ' . (function_exists('password_hash') ? 'DISPONIBLE' : 'NO DISPONIBLE') . '</p>';
$diagnostic .= '<p>password_verify(): ' . (function_exists('password_verify') ? 'DISPONIBLE' : 'NO DISPONIBLE') . '</p>';
$diagnostic .= '</div>';
echo $diagnostic;

// 5. Verificar archivo users.json
$diagnostic = '<div class="diagnostic ' . (file_exists('users.json') ? 'pass' : 'fail') . '">';
$diagnostic .= '<h2>' . (file_exists('users.json') ? '✓' : '✗') . ' Archivo users.json</h2>';
$diagnostic .= '<p>Existe: ' . (file_exists('users.json') ? 'SÍ' : 'NO') . '</p>';
if (file_exists('users.json')) {
    $diagnostic .= '<p>Legible: ' . (is_readable('users.json') ? 'SÍ' : 'NO') . '</p>';
    $diagnostic .= '<p>Tamaño: ' . filesize('users.json') . ' bytes</p>';
    
    // Verificar JSON válido
    $json = file_get_contents('users.json');
    $users = json_decode($json, true);
    $diagnostic .= '<p>JSON válido: ' . (json_last_error() === JSON_ERROR_NONE ? 'SÍ' : 'NO - ' . json_last_error_msg()) . '</p>';
    if (json_last_error() === JSON_ERROR_NONE) {
        $diagnostic .= '<p>Usuarios encontrados: ' . count($users) . '</p>';
    }
}
$diagnostic .= '</div>';
echo $diagnostic;

// 6. Verificar directorio logs/
$logs_exist = is_dir('logs');
$diagnostic = '<div class="diagnostic ' . ($logs_exist ? 'pass' : 'warn') . '">';
$diagnostic .= '<h2>' . ($logs_exist ? '✓' : '⚠') . ' Directorio logs/</h2>';
$diagnostic .= '<p>Existe: ' . ($logs_exist ? 'SÍ' : 'NO (se creará automáticamente)') . '</p>';
if ($logs_exist) {
    $diagnostic .= '<p>Escribible: ' . (is_writable('logs') ? 'SÍ' : 'NO') . '</p>';
}
$diagnostic .= '</div>';
echo $diagnostic;

// 7. Probar creación de sesión
session_start();
$_SESSION['diagnostico'] = 'test_' . time();
$session_id = session_id();

$diagnostic = '<div class="diagnostic ' . (!empty($session_id) ? 'pass' : 'fail') . '">';
$diagnostic .= '<h2>' . (!empty($session_id) ? '✓' : '✗') . ' Test de Sesión</h2>';
$diagnostic .= '<p>Session ID: <code>' . htmlspecialchars($session_id) . '</code></p>';
$diagnostic .= '<p>Datos grabados: ' . (isset($_SESSION['diagnostico']) ? 'SÍ' : 'NO') . '</p>';
$diagnostic .= '</div>';
echo $diagnostic;

// 8. Verificar header() function
$diagnostic = '<div class="diagnostic ' . (function_exists('header') ? 'pass' : 'fail') . '">';
$diagnostic .= '<h2>' . (function_exists('header') ? '✓' : '✗') . ' Función header()</h2>';
$diagnostic .= '<p>Disponible: ' . (function_exists('header') ? 'SÍ (redirecciones OK)' : 'NO (problema)') . '</p>';
$diagnostic .= '<p>Headers enviados: ' . (headers_sent() ? 'SÍ (puede afectar redirecciones)' : 'NO') . '</p>';
$diagnostic .= '</div>';
echo $diagnostic;

// 9. Verificar JSON en users.json detalladamente
if (file_exists('users.json') && is_readable('users.json')) {
    $json = file_get_contents('users.json');
    $users = json_decode($json, true);
    
    if (is_array($users) && count($users) > 0) {
        $diagnostic = '<div class="diagnostic pass"><h2>✓ Credenciales de Prueba</h2>';
        foreach ($users as $user) {
            $diagnostic .= '<p><strong>' . htmlspecialchars($user['username']) . '</strong></p>';
            $diagnostic .= '<p style="margin-left:20px;">Email: ' . htmlspecialchars($user['email']) . '</p>';
            
            // Verificar si el hash de "123456" coincide
            $test_password = '123456';
            $hash_matches = password_verify($test_password, $user['password']);
            $diagnostic .= '<p style="margin-left:20px;">Hash válido para "123456": ' . ($hash_matches ? '✓ SÍ' : '✗ NO') . '</p>';
        }
        $diagnostic .= '</div>';
        echo $diagnostic;
    }
}

// 10. Mensajes de error si los hay
if (ini_get('display_errors')) {
    $diagnostic = '<div class="diagnostic warn"><h2>⚠ Error Reporting Activo</h2>';
    $diagnostic .= '<p>Los errores se mostrarán en la página (bien para desarrollo, malo para producción)</p>';
    $diagnostic .= '</div>';
    echo $diagnostic;
}

echo "</body></html>";
?>

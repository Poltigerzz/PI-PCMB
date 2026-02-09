<?php
/**
 * Configuración Global del Proyecto
 * 
 * @package PHP-MBPC
 * @file config.php
 * @version 1.0.0
 */

// ============================================
// CONFIGURACIÓN DE RUTAS
// ============================================

// Detectar la ruta base del sitio automáticamente
if (!isset($GLOBALS['BASE_URL'])) {
    // Obtener ruta del protocolo y host
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    
    // Detectar si está en un subdirectorio
    $scriptPath = dirname($_SERVER['SCRIPT_NAME']);
    $basePath = rtrim($scriptPath, '/');
    
    $GLOBALS['PROTOCOL'] = $protocol;
    $GLOBALS['HOST'] = $host;
    $GLOBALS['BASE_PATH'] = $basePath;
    $GLOBALS['BASE_URL'] = $protocol . '://' . $host . $basePath;
}

// ============================================
// CONFIGURACIÓN DE SESIÓN
// ============================================

// Usar solo cookies para sesiones (más seguro)
ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 1);

// Más seguridad
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_lifetime', 0); // Cookie de sesión (se borra al cerrar navegador)

// Determinar si usar HTTPS
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    ini_set('session.cookie_secure', 1);
}

// ============================================
// FUNCIONES GLOBALES
// ============================================

/**
 * Obtener URL completa con ruta base
 */
function getUrl($path = '') {
    $base = $GLOBALS['BASE_URL'];
    if ($path) {
        return rtrim($base, '/') . '/' . ltrim($path, '/');
    }
    return $base;
}

/**
 * Redirigir con ruta base
 */
function redirect($path) {
    $url = getUrl($path);
    header("Location: $url");
    exit();
}

/**
 * Obtener ruta relativa
 */
function getPath($file) {
    $basePath = $GLOBALS['BASE_PATH'];
    return $basePath ? $basePath . '/' . ltrim($file, '/') : '/' . ltrim($file, '/');
}

// ============================================
// INICIAR SESIÓN SI NO ESTÁ INICIADA
// ============================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

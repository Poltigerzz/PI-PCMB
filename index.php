<?php
/**
 * Página Principal - Redirección a Login
 * 
 * @package PHP-MBPC
 * @file index.php
 * @version 1.0.0
 */

// Redirigir a login.html
header('Location: login.html');
exit();

// ============================================
// 2. Procesar logout
// ============================================

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    setcookie('username', '', time() - 3600, '/');
    header('Location: login.html');
    exit();
}

// ============================================
// 3. Obtener datos de sesión
// ============================================

// Fallbacks por si falta algo en sesión
$_SESSION['username']   = $_SESSION['username']   ?? 'user';
$_SESSION['email']      = $_SESSION['email']      ?? 'user@example.com';
$_SESSION['name']       = $_SESSION['name']       ?? 'Usuario';
$_SESSION['role']       = $_SESSION['role']       ?? 'student';
$_SESSION['login_time'] = $_SESSION['login_time'] ?? time();
$_SESSION['ip_address'] = $_SESSION['ip_address'] ?? ($_SERVER['REMOTE_ADDR'] ?? 'unknown');

$user = [
    'id' => $_SESSION['user_id'],
    'username' => $_SESSION['username'],
    'email' => $_SESSION['email'],
    'name' => $_SESSION['name'],
    'role' => $_SESSION['role'],
    'login_time' => $_SESSION['login_time'],
    'ip_address' => $_SESSION['ip_address']
];

// Formatear fecha/hora login
$loginDate = date('d/m/Y H:i:s', $user['login_time']);

// Escapar para HTML
function e($str) {
  return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard :: <?php echo e($user['username']); ?></title>
  <link rel="stylesheet" href="styles.css">
  <style>
    /* E*


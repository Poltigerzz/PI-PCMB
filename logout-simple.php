<?php
session_start();

// Destruir sesion
$_SESSION = array();
session_destroy();

// Eliminar cookies
setcookie('username', '', time() - 3600, '/');

// Redirigir a login
header('Location: login-simple.php');
exit;
?>

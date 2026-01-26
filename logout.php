<?php
session_start();

// Eliminar todas las variables de sesión
$_SESSION = array();

// Destruir la sesión
session_destroy();

// Eliminar cookies de "remember me"
setcookie('username', '', time() - 3600, '/');

// Redirigir a login
header('Location: login.html');
exit;
?>

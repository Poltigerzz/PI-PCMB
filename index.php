<?php
/**
 * Dashboard - Página Principal Protegida
 * 
 * @package PHP-MBPC
 * @file dashboard.php
 * @version 1.0.0
 */

session_start();

// ============================================
// 1. Verificar autenticación
// ============================================

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}

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
    /* Extra para dashboard */
    .grid{
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 18px;
      margin-top: 18px;
    }
    .panel{
      background: rgba(0,0,0,0.55);
      border: 1px solid rgba(0,255,170,0.35);
      border-radius: 14px;
      padding: 16px;
      position: relative;
      overflow: hidden;
    }
    .panel h3{
      margin-top: 0;
      margin-bottom: 10px;
      font-size: 1.05rem;
    }
    .kv{
      display: grid;
      grid-template-columns: 110px 1fr;
      gap: 8px 12px;
      font-size: 0.92rem;
      color: rgba(180,255,220,0.8);
    }
    .kv div:nth-child(odd){
      color: rgba(0,255,170,0.9);
    }
    .actions{
      display:flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-top: 14px;
    }
    .btn{
      display:inline-block;
      padding: 10px 12px;
      border-radius: 10px;
      border: 1px solid rgba(0,255,170,0.5);
      color: rgba(0,255,170,0.95);
      background: rgba(0,0,0,0.35);
      text-decoration: none;
    }
    .btn:hover{
      box-shadow: 0 0 12px rgba(0,255,170,0.35);
    }
    .tag{
      display:inline-block;
      padding: 3px 10px;
      border-radius: 999px;
      font-size: 0.82rem;
      border: 1px solid rgba(0,195,255,0.45);
      color: rgba(0,195,255,0.95);
      background: rgba(0,195,255,0.08);
      margin-left: 8px;
      vertical-align: middle;
    }
  </style>
</head>
<body>
  <div class="card">

    <div class="topbar">
      <strong>🛡️ CyberEdu :: Panel</strong>
      <div class="menu">
        <a href="index.php">[ HOME ]</a> ·
        <a href="dashboard.php?action=logout">[ LOGOUT ]</a>
      </div>
    </div>

    <h1>Acceso concedido</h1>
    <p class="sub">
      Bienvenido/a, <b><?php echo e($user['name']); ?></b>
      <span class="tag"><?php echo e($user['role']); ?></span>
    </p>

    <div class="grid">
      <div class="panel">
        <h3>👤 Perfil</h3>
        <div class="kv">
          <div>ID</div><div><?php echo e($user['id']); ?></div>
          <div>Usuario</div><div><?php echo e($user['username']); ?></div>
          <div>Email</div><div><?php echo e($user['email']); ?></div>
          <div>Rol</div><div><?php echo e($user['role']); ?></div>
        </div>
      </div>

      <div class="panel">
        <h3>🛰️ Sesión</h3>
        <div class="kv">
          <div>Login</div><div><?php echo e($loginDate); ?></div>
          <div>IP</div><div><?php echo e($user['ip_address']); ?></div>
          <div>Estado</div><div>ONLINE</div>
        </div>

        <div class="actions">
          <a class="btn" href="index.php">Ir a Home</a>
          <a class="btn" href="dashboard.php?action=logout">Cerrar sesión</a>
        </div>
      </div>

      <div class="panel">
        <h3>🧪 Servicios (ficticios)</h3>
        <p class="small">
          Esta sección es solo demostrativa/educativa. No se realizan ataques reales.
        </p>
        <ul class="small" style="margin:0; padding-left:18px;">
          <li>Simulación teórica de DDoS</li>
          <li>Monitoreo de tráfico (ejemplos)</li>
          <li>Estrategias de defensa y mitigación</li>
          <li>Práctica de sesiones y autenticación en PHP</li>
        </ul>
      </div>
    </div>

    <h2 style="margin-top:26px;">⚠️ Aviso legal</h2>
    <p class="small">
      Proyecto 100% educativo. No se promueven actividades ilegales.
      Cualquier referencia a DDoS es solo para concienciación y aprendizaje.
    </p>

  </div>
</body>
</html>

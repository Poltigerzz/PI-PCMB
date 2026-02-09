<?php
/**
 * Backend de Autenticacion - SIMPLE VERSION
 * Sin dependencias externas
 */

// Iniciar sesion
session_start();

// Definir zona horaria
date_default_timezone_set('Europe/Madrid');

// Respuesta JSON
$response = array(
    'success' => false,
    'message' => '',
    'redirect' => null
);

// Si ya esta autenticado, redirigir al panel
if (isset($_SESSION['user_id'])) {
    header('Location: panel-simple.php');
    exit();
}

// Procesar POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $remember = isset($_POST['remember']);
    
    // Validaciones
    if (empty($username)) {
        $response['message'] = 'El usuario o email es requerido';
    } 
    elseif (empty($password)) {
        $response['message'] = 'La contraseña es requerida';
    }
    else {
        // Leer usuarios desde JSON
        $users_file = 'users.json';
        
        if (file_exists($users_file)) {
            $json_data = file_get_contents($users_file);
            $users = json_decode($json_data, true);
            
            if (is_array($users)) {
                $user_found = null;
                
                // Buscar usuario
                foreach ($users as $user) {
                    if ($user['username'] === $username || $user['email'] === $username) {
                        $user_found = $user;
                        break;
                    }
                }
                
                // Verificar contraseña
                if ($user_found !== null && password_verify($password, $user_found['password'])) {
                    
                    // Crear sesion
                    $_SESSION['user_id'] = $user_found['id'];
                    $_SESSION['username'] = $user_found['username'];
                    $_SESSION['email'] = $user_found['email'];
                    $_SESSION['name'] = $user_found['name'];
                    $_SESSION['role'] = $user_found['role'];
                    $_SESSION['logged_in'] = true;
                    $_SESSION['login_time'] = time();
                    
                    // Cookie de recuerdo
                    if ($remember) {
                        setcookie('username', $user_found['username'], time() + (86400 * 30), '/');
                    }
                    
                    $response['success'] = true;
                    $response['message'] = 'Login exitoso';
                    $response['redirect'] = 'panel.php';
                    
                    // Registrar en log
                    if (!file_exists('logs')) {
                        mkdir('logs', 0755, true);
                    }
                    $log_msg = date('Y-m-d H:i:s') . ' [info] Login exitoso para usuario: ' . $user_found['username'] . PHP_EOL;
                    file_put_contents('logs/auth.log', $log_msg, FILE_APPEND);
                    
                } else {
                    $response['message'] = 'Usuario o contraseña incorrecta';
                    
                    // Registrar intento fallido
                    if (!file_exists('logs')) {
                        mkdir('logs', 0755, true);
                    }
                    $log_msg = date('Y-m-d H:i:s') . ' [warning] Intento de login fallido para: ' . $username . PHP_EOL;
                    file_put_contents('logs/auth.log', $log_msg, FILE_APPEND);
                }
            }
        } else {
            $response['message'] = 'Archivo de usuarios no encontrado';
        }
    }
    
    // Si es AJAX, retornar JSON
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($response);
        exit();
    }
    
    // Si es exitoso, redirigir
    if ($response['success']) {
        header('Location: panel-simple.php');
        exit();
    }
}

// Cookie recordada
$remembered_username = isset($_COOKIE['username']) ? $_COOKIE['username'] : '';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesion</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <canvas id="matrix" width="800" height="600"></canvas>

    <div class="container">
        <div class="logo">
            <h1 class="glitch" data-text="ROOT@SISTEMA">ROOT@SISTEMA</h1>
            <p class="subtitle">ACCES NO AUTORITZAT SERA REGISTRAT</p>
        </div>

        <?php if (!empty($response['message']) && !$response['success']): ?>
            <div class="error-message" style="display: block;">
                <?php echo htmlspecialchars($response['message']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php" id="loginForm">
            
            <div class="form-group">
                <label for="username">[ USUARI / CORREU ]</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    placeholder="INTRODUEIX L'IDENTIFICADOR..."
                    value="<?php echo htmlspecialchars($remembered_username); ?>"
                    required
                    autocomplete="off"
                >
            </div>

            <div class="form-group">
                <label for="password">[ CONTRASENYA ]</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="************"
                    required
                    autocomplete="off"
                >
            </div>

            <div class="form-group checkbox">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">MANTE LA SESSIO</label>
            </div>

            <button type="submit" class="submit-btn">EXECUTA L'ACCES</button>

        </form>

        <div class="divider">// OPCIONS DE BRETxA //</div>

        <div class="footer-links">
            <a href="#">CREA USUARI NOU</a>
            <a href="#">RECUPERA CREDENCIALS</a>
        </div>

        <!-- Dades de prova  -->
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ccc; font-size: 12px; color: #666;">
            <p><strong>Credencials de prova:</strong></p>
            <p>Usuari: <code>admin</code> | Contrasenya: <code>123456</code></p>
            <p>Usuari: <code>usuario</code> | Contrasenya: <code>123456</code></p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const canvas = document.getElementById('matrix');
            const ctx = canvas.getContext('2d');

            function resizeCanvas() {
                canvas.height = window.innerHeight;
                canvas.width = window.innerWidth;
            }
            
            resizeCanvas();
            window.addEventListener('resize', resizeCanvas);

            const chars = '01アイウエオカキクケコサシスセソタチツテトナニヌネノハヒフヘホマミムメモヤユヨラリルレロワヲン█▓▒░→←↑↓↔⚡★☆■□▪▫';
            const fontSize = 14;
            const columns = Math.floor(canvas.width / fontSize);
            const drops = new Array(columns).fill(1);

            function draw() {
                ctx.fillStyle = 'rgba(0, 0, 0, 0.05)';
                ctx.fillRect(0, 0, canvas.width, canvas.height);

                ctx.fillStyle = '#0f0';
                ctx.font = fontSize + 'px Fira Code, monospace';

                for (let i = 0; i < columns; i++) {
                    const char = chars.charAt(Math.floor(Math.random() * chars.length));
                    const x = i * fontSize;
                    const y = drops[i] * fontSize;

                    ctx.fillText(char, x, y);

                    if (y > canvas.height && Math.random() > 0.975) {
                        drops[i] = 0;
                    }

                    drops[i]++;
                }
            }

            setInterval(draw, 35);
        });
    </script>
</body>
</html>

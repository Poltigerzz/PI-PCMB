<?php
/**
 * Backend de Autenticación
 * 
 * @package PHP-MBPC
 * @file login.php
 * @version 1.0.0
 */

// ============================================
// 1. Configuración y Sesión
// ============================================

session_start();

// Definir zona horaria
date_default_timezone_set('Europe/Madrid');

// ============================================
// 2. Funciones Auxiliares
// ============================================

/**
 * Sanitizar entrada del usuario
 * 
 * @param string $input
 * @return string
 */
function sanitize($input) {
    return htmlspecialchars(stripslashes(trim($input)));
}

/**
 * Validar email
 * 
 * @param string $email
 * @return bool
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Registrar evento en log
 * 
 * @param string $message
 * @param string $type
 */
function logEvent($message, $type = 'info') {
    $timestamp = date('Y-m-d H:i:s');
    $logFile = 'logs/auth.log';
    
    if (!file_exists('logs')) {
        mkdir('logs', 0755, true);
    }
    
    $logMessage = "[$timestamp] [$type] $message" . PHP_EOL;
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

/**
 * Obtener usuarios desde JSON
 * 
 * @return array
 */
function getUsers() {
    $usersFile = 'users.json';
    
    if (!file_exists($usersFile)) {
        logEvent('Archivo users.json no encontrado', 'error');
        return [];
    }
    
    $json = file_get_contents($usersFile);
    $users = json_decode($json, true);
    
    if (!is_array($users)) {
        logEvent('Error al decodificar users.json', 'error');
        return [];
    }
    
    return $users;
}

/**
 * Buscar usuario por username o email
 * 
 * @param string $identifier Username o email
 * @return array|null
 */
function findUser($identifier) {
    $users = getUsers();
    
    foreach ($users as $user) {
        if ($user['username'] === $identifier || $user['email'] === $identifier) {
            return $user;
        }
    }
    
    return null;
}

// ============================================
// 3. Procesar Autenticación
// ============================================

$response = [
    'success' => false,
    'message' => '',
    'redirect' => null
];

// Verificar que sea una solicitud POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Obtener y sanitizar datos
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    // Validaciones
    if (empty($username)) {
        $response['message'] = 'El usuario o email es requerido';
        logEvent("Intento de login sin usuario desde {$_SERVER['REMOTE_ADDR']}", 'warning');
    } 
    elseif (empty($password)) {
        $response['message'] = 'La contraseña es requerida';
        logEvent("Intento de login sin contraseña desde {$_SERVER['REMOTE_ADDR']}", 'warning');
    } 
    elseif (strlen($password) < 6) {
        $response['message'] = 'La contraseña debe tener al menos 6 caracteres';
    }
    else {
        // Buscar usuario
        $user = findUser($username);
        
        if ($user === null) {
            $response['message'] = 'Usuario o contraseña incorrecta';
            logEvent("Intento de login fallido para: $username desde {$_SERVER['REMOTE_ADDR']}", 'warning');
        }
        elseif (!password_verify($password, $user['password'])) {
            $response['message'] = 'Usuario o contraseña incorrecta';
            logEvent("Contraseña incorrecta para usuario: {$user['username']} desde {$_SERVER['REMOTE_ADDR']}", 'warning');
        }
        else {
            // Autenticación exitosa
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['logged_in'] = true;
            $_SESSION['login_time'] = time();
            $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
            
            // Recordar datos si está marcado
            if ($remember) {
                setcookie('username', $user['username'], time() + (86400 * 30), '/'); // 30 días
            }
            
            $response['success'] = true;
            $response['message'] = 'Login exitoso';
            $response['redirect'] = 'panel.php';
            
            logEvent("Login exitoso para usuario: {$user['username']} desde {$_SERVER['REMOTE_ADDR']}", 'info');
        }
    }
    
    // Retornar respuesta JSON si es AJAX
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
    
    // Si no es AJAX y fue exitoso, redirigir
    if ($response['success']) {
        header('Location: ' . $response['redirect']);
        exit();
    }
    
} elseif ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    // Método no permitido
    http_response_code(405);
    die('Método no permitido');
}

// ============================================
// 4. Verificar si ya está autenticado
// ============================================

if (isset($_SESSION['user_id'])) {
    header('Location: panel.php');
    exit();
}

// ============================================
// 5. Obtener username recordado si existe
// ============================================

$remembered_username = $_COOKIE['username'] ?? '';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="container">
        <div class="logo">
            <h1>🔐 Login</h1>
            <p>Bienvenido a tu aplicación</p>
        </div>

        <?php if (!$response['success'] && !empty($response['message'])): ?>
            <div id="errorMessage" class="error-message" style="display: block;">
                <?php echo htmlspecialchars($response['message']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php" id="loginForm" onsubmit="return validateForm()">
            
            <div class="form-group">
                <label for="username">Usuario o Email</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    placeholder="Ingresa tu usuario o email"
                    value="<?php echo htmlspecialchars($remembered_username); ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="Ingresa tu contraseña"
                    required
                >
            </div>

            <div class="form-group checkbox">
                <input 
                    type="checkbox" 
                    id="remember" 
                    name="remember"
                >
                <label for="remember">Recuerda mis datos</label>
            </div>

            <button type="submit" class="submit-btn">Iniciar Sesión</button>

        </form>

        <div class="divider">o</div>

        <div class="footer-links">
            <a href="register.html">Regístrate</a>
            <a href="forgot-password.html">¿Olvidaste tu contraseña?</a>
        </div>

        <!-- Datos de prueba -->
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; font-size: 12px; color: #999;">
            <p><strong>Credenciales de prueba:</strong></p>
            <p>Usuario: <code>admin</code> | Contraseña: <code>123456</code></p>
            <p>Usuario: <code>usuario</code> | Contraseña: <code>123456</code></p>
        </div>
    </div>

    <script>
        function validateForm() {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;
            const errorDiv = document.getElementById('errorMessage');

            // Limpiar mensajes previos
            if (!errorDiv) {
                const div = document.createElement('div');
                div.id = 'errorMessage';
                div.className = 'error-message';
                document.querySelector('.container').insertBefore(div, document.querySelector('form'));
            }

            const error = document.getElementById('errorMessage');
            error.style.display = 'none';
            error.textContent = '';

            if (!username) {
                showError('Por favor ingresa tu usuario o email');
                return false;
            }

            if (!password) {
                showError('Por favor ingresa tu contraseña');
                return false;
            }

            if (password.length < 6) {
                showError('La contraseña debe tener al menos 6 caracteres');
                return false;
            }

            return true;
        }

        function showError(message) {
            let errorDiv = document.getElementById('errorMessage');
            
            if (!errorDiv) {
                errorDiv = document.createElement('div');
                errorDiv.id = 'errorMessage';
                errorDiv.className = 'error-message';
                document.querySelector('.container').insertBefore(errorDiv, document.querySelector('form'));
            }
            
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
        }

        document.getElementById('loginForm').addEventListener('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>

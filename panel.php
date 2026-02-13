<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Usuario';
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panell de Control // CyberEdu</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <canvas id="matrix" width="800" height="600" style="opacity: 0.1;"></canvas>

    <div class="card">
        <div class="topbar">
            <strong>Panell de Control</strong>
            <div style="display: flex; gap: 20px; align-items: center;">
                <span>Hola, <?php echo $username; ?></span>
                <a href="logout.php" style="color: #0f0; text-decoration: none;">Tancar Sessio</a>
            </div>
        </div>

        <div class="logo">
            <h1 class="glitch" data-text="TERMINAL DE CONTROL">TERMINAL DE CONTROL</h1>
            <p class="subtitle">Eines educatives i simulacions de seguretat</p>
        </div>

        <div class="services">
            <div class="service">
                <h3>DDoS Simulator</h3>
                <p class="small">Simula un atac DDoS a un servidor fictici</p>
                <button class="submit-btn" onclick="alert('Simulador disponible en proxima versio')">Iniciar Simulacio</button>
            </div>

            <div class="service">
                <h3>SQL Injection Test</h3>
                <p class="small">Aprens els riscos de SQL Injection</p>
                <button class="submit-btn" onclick="alert('Simulador disponible en proxima versio')">Iniciar Simulacio</button>
            </div>

            <div class="service">
                <h3>XSS Simulator</h3>
                <p class="small">Entens els atacs Cross-Site Scripting</p>
                <button class="submit-btn" onclick="alert('Simulador disponible en proxima versio')">Iniciar Simulacio</button>
            </div>

            <div class="service">
                <h3>Password Strength</h3>
                <p class="small">Analitza la forta de contrasenyes</p>
                <button class="submit-btn" onclick="alert('Simulador disponible en proxima versio')">Iniciar Simulacio</button>
            </div>

            <div class="service">
                <h3>Network Scanner</h3>
                <p class="small">Simula un analitis de xarxa</p>
                <button class="submit-btn" onclick="alert('Simulador disponible en proxima versio')">Iniciar Simulacio</button>
            </div>

            <div class="service">
                <h3>Firewall Rules</h3>
                <p class="small">Configura firewalls virtuals</p>
                <button class="submit-btn" onclick="alert('Simulador disponible en proxima versio')">Iniciar Simulacio</button>
            </div>

            <div class="service">
                <h3>Encryption Tools</h3>
                <p class="small">Aprens sobre criptografia</p>
                <button class="submit-btn" onclick="alert('Simulador disponible en proxima versio')">Iniciar Simulacio</button>
            </div>

            <div class="service">
                <h3>Penetration Test</h3>
                <p class="small">Simula un test de penetracio</p>
                <button class="submit-btn" onclick="alert('Simulador disponible en proxima versio')">Iniciar Simulacio</button>
            </div>
        </div>

        <div style="margin-top: 3rem; padding: 1.5rem; background: rgba(0,255,0,0.08); border: 2px solid #0f0; border-radius: 8px;">
            <h2 style="margin-bottom: 1rem; text-align: center;">Estadistiques del Sistema</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                <div style="padding: 1rem; background: rgba(0,255,0,0.1); border-radius: 4px;">
                    <p style="margin: 0; color: #0a0;">Sessions Actives</p>
                    <p style="margin: 5px 0 0; font-size: 24px; color: #0f0;">1</p>
                </div>
                <div style="padding: 1rem; background: rgba(0,255,0,0.1); border-radius: 4px;">
                    <p style="margin: 0; color: #0a0;">Usuaris Conectats</p>
                    <p style="margin: 5px 0 0; font-size: 24px; color: #0f0;">1</p>
                </div>
                <div style="padding: 1rem; background: rgba(0,255,0,0.1); border-radius: 4px;">
                    <p style="margin: 0; color: #0a0;">Simulacions Totals</p>
                    <p style="margin: 5px 0 0; font-size: 24px; color: #0f0;">0</p>
                </div>
                <div style="padding: 1rem; background: rgba(0,255,0,0.1); border-radius: 4px;">
                    <p style="margin: 0; color: #0a0;">Temps en Linia</p>
                    <p style="margin: 5px 0 0; font-size: 24px; color: #0f0;">--</p>
                </div>
            </div>
        </div>

        <div style="margin-top: 2rem; text-align: center;">
            <a href="index.php" style="color: #0f0; text-decoration: none;">Tornar al Inici</a>
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

            function drawMatrix() {
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

            setInterval(drawMatrix, 35);
        });
    </script>

</body>
</html>

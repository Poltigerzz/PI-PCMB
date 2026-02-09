<?php
/**
 * Pagina Principal - SIMPLE VERSION
 */

session_start();

date_default_timezone_set('Europe/Madrid');

// Verificar autenticacion
if (!isset($_SESSION['user_id'])) {
    header('Location: login-simple.php');
    exit();
}

// Procesar logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    setcookie('username', '', time() - 3600, '/');
    header('Location: login-simple.php');
    exit();
}

$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Usuario';
?>
<!doctype html>
<html lang="ca">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CyberEdu // Laboratori Hacker Educatiu</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>

    <canvas id="matrix" width="800" height="600"></canvas>

    <div class="card">

        <div class="topbar">
            <strong>CYBEREDU :: LABORATORI HACKER EDUCATIU</strong>
            <div style="display: flex; gap: 20px; align-items: center;">
                <span>Hola, <?php echo $username; ?></span>
                <a href="?action=logout" style="color: #0f0; text-decoration: none;">Tancar Sessio</a>
            </div>
        </div>

        <h1 class="glitch" data-text="SIMULACIO D'ATACS CIBERNEICS">
            SIMULACIO D'ATACS CIBERNEICS
        </h1>
        
        <p class="sub">
            Entorn educatiu i fictici per aprendre desenvolupament web segur en PHP
        </p>

        <div style="text-align: center; margin: 3rem 0 4rem;">
            <a href="panel-simple.php" class="big-control-btn">
                Accedeix al Panell de Control
            </a>
        </div>

        <h2>Objectiu del sistema</h2>
        <p class="small">
            Aquesta plataforma simula serveis de ciberseguretat amb finalitats academiques.
            No es realitzen atacs reals ni s'ofereixen serveis il.legals.
        </p>

        <h2>Que es especialment un atac DDoS?</h2>
        <p class="small">
            Un atac DDoS consisteix a enviar una quantitat enorme de peticions falses des de molts dispositius
            per saturar l'amplada de banda, els recursos del servidor o les aplicacions.
        </p>

        <p class="small" style="margin-top: 1rem;">
            <b>Tipus principals:</b>
        </p>
        <ul class="small" style="padding-left: 1.8rem; margin: 0.8rem 0; line-height: 1.6;">
            <li><b>Volumerics</b> - UDP floods, ICMP floods, DNS amplification</li>
            <li><b>De protocol</b> - SYN flood, ACK flood, UDP fragmentacio</li>
            <li><b>D'aplicacio</b> - HTTP GET/POST floods, Slowloris, RUDY</li>
        </ul>

        <h2 class="warning-title">AVIS LEGAL IMPORTANT</h2>
        <p class="small">
            Aquest projecte es 100% fictici i te finalitats educatives.
            No es realitzen atacs reals ni s'ofereixen serveis il.legals.
        </p>

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
                    const text = chars.charAt(Math.floor(Math.random() * chars.length));
                    const x = i * fontSize;
                    const y = drops[i] * fontSize;

                    ctx.fillText(text, x, y);

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

<?php
/**
 * Página Principal
 * 
 * @package PHP-MBPC
 * @file index.php
 * @version 1.0.0
 */

// Incluir configuración global
require_once __DIR__ . '/config.php';

// Definir zona horaria
date_default_timezone_set('Europe/Madrid');

// ============================================
// Verificar autenticación
// ============================================

if (!isset($_SESSION['user_id'])) {
    // No está autenticado, redirigir a login
    redirect('login.php');
    exit();
}

// ============================================
// Procesar logout
// ============================================

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    setcookie('username', '', time() - 3600, '/');
    redirect('login.php');
    exit();
}

// ============================================
// Función para escapar HTML
// ============================================

function e($str) {
  return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}
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
      <strong>🛡️ CYBEREDU :: LABORATORI HACKER EDUCATIU</strong>
      <div style="display: flex; gap: 20px; align-items: center;">
        <span style="color: #0c0; font-size: 0.9rem;">Usuari: <?php echo e($_SESSION['username']); ?></span>
        <a href="?action=logout" style="color: #0f0; text-decoration: none; padding: 8px 12px; border: 1px solid #0f0; border-radius: 4px; transition: all 0.3s; cursor: pointer;" 
           onmouseover="this.style.background='#0f0'; this.style.color='#000';" 
           onmouseout="this.style.background='transparent'; this.style.color='#0f0';">
          TANCAR SESSIÓ
        </a>
      </div>
    </div>

    <h1 class="glitch" data-text="SIMULACIÓ D'ATACS CIBERNÈTICS & DEFENSA">
      SIMULACIÓ D'ATACS CIBERNÈTICS & DEFENSA
    </h1>
    
    <p class="sub">
      Entorn educatiu i fictici per aprendre desenvolupament web segur en PHP
      i comprendre els mecanismes i riscos dels atacs DDoS i altres amenaces.
    </p>

    <!-- Botó afegit per anar al panell -->
    <div style="text-align: center; margin: 3rem 0 4rem;">
      <a href="panel.php" class="big-control-btn">
        ACCEDIR AL PANELL DE CONTROL →
      </a>
    </div>

    <h2>Objectiu del sistema</h2>
    <p class="small">
      Aquesta plataforma simula serveis de ciberseguretat amb finalitats 100% acadèmiques i pedagògiques.<br>
      No es realitzen atacs reals, no s'emmagatzemen dades sensibles i no s'ofereixen serveis il·legals.<br>
      L'objectiu és formar desenvolupadors, estudiants i curiosos en seguretat web mitjançant exemples pràctics i teòrics.
    </p>

    <h2>Conceptes bàsics que cal conèixer</h2>
    <div class="services">

      <div class="service">
        <h3>Phishing</h3>
        <p class="small">
          Engany mitjançant correus, SMS, webs falses o trucades per obtenir credencials, instal·lar malware o fer transferències fraudulentes.
        </p>
      </div>

      <div class="service">
        <h3>Malware / Ransomware</h3>
        <p class="small">
          Programari maliciós dissenyat per danyar sistemes, robar informació, xifrar fitxers i demanar rescat (ransomware) o actuar com a backdoor.
        </p>
      </div>

      <div class="service">
        <h3>SQL Injection</h3>
        <p class="small">
          Injecció de codi maliciós en camps d'entrada per manipular consultes SQL: extreure dades, modificar registres o esborrar taules senceres.
        </p>
      </div>

      <div class="service">
        <h3>XSS (Cross-Site Scripting)</h3>
        <p class="small">
          Inserció de scripts maliciosos que s'executen al navegador de la víctima: robatori de cookies, keylogging, defacement o phishing intern.
        </p>
      </div>

      <div class="service">
        <h3>Brute Force / Credential Stuffing</h3>
        <p class="small">
          Atacs automatitzats provant milers de combinacions de contrasenyes o reutilitzant credencials filtrades d'altres bretxes.
        </p>
      </div>

      <div class="service">
        <h3>DDoS (Distributed Denial of Service)</h3>
        <p class="small">
          Saturació d'un servidor, aplicació o xarxa amb tràfic massiu provinent de múltiples fonts (botnet) per fer-lo inaccessible als usuaris legítims.
        </p>
      </div>

    </div>

    <h2>Què és realment un atac DDoS?</h2>
    <p class="small">
      Un atac DDoS consisteix a enviar una quantitat enorme de peticions falses des de molts dispositius diferents
      (normalment una botnet controlada per un C&C) per saturar l'ample de banda, els recursos del servidor o les aplicacions.
    </p>

    <p class="small" style="margin-top: 1rem;">
      <b>Tipus principals:</b>
    </p>
    <ul class="small" style="padding-left: 1.8rem; margin: 0.8rem 0; line-height: 1.6;">
      <li><b>Volumètrics</b> → UDP floods, ICMP floods, DNS amplification, NTP amplification</li>
      <li><b>De protocol</b> → SYN flood, ACK flood, UDP fragmentació, Ping of Death</li>
      <li><b>D'aplicació (Layer 7)</b> → HTTP GET/POST floods, Slowloris, RUDY, XML/JSON bomb</li>
    </ul>

    <p class="small" style="margin-top: 1rem;">
      <b>Conseqüències típiques:</b> indisponibilitat total o parcial del servei, pèrdua d'ingressos, danys a la reputació,
      costos elevats de mitigació i, en alguns casos, distracció per realitzar altres atacs simultanis (ex: ransomware).
    </p>

    <h2>Serveis simulats en aquesta plataforma</h2>
    <div class="services">

      <div class="service">
        <h3>Simulació DDoS</h3>
        <p class="small">
          Entendre el comportament teòric i visualitzar l'impacte en sistemes reals (sense tràfic real).
        </p>
      </div>

      <div class="service">
        <h3>Monitorització de Tràfic</h3>
        <p class="small">
          Exemples ficticis d'anàlisi de logs i detecció de patrons anòmals / anomalies.
        </p>
      </div>

      <div class="service">
        <h3>Estratègies de Defensa</h3>
        <p class="small">
          Rate limiting, WAF, CDN, blackholing, Geo-blocking, CAPTCHA adaptatiu, scrubbing...
        </p>
      </div>

      <div class="service">
        <h3>Entrenament PHP segur</h3>
        <p class="small">
          Sessions segures, validació estricta, prepared statements, hashing (bcrypt/argon2), protecció CSRF, headers de seguretat...
        </p>
      </div>

    </div>

    <h2>10 regles bàsiques de seguretat (que hauries de seguir avui)</h2>
    <ol class="small" style="padding-left: 1.8rem; margin: 1.2rem 0; line-height: 1.7;">
      <li>Contrasenya llarga i única → mínim 14–16 caràcters + gestor de contrasenyes</li>
      <li>Activa 2FA/MFA a tot arreu on sigui possible</li>
      <li>Mantingues actualitzat el sistema operatiu, navegador, plugins i aplicacions</li>
      <li>No cliquis enllaços ni obris adjunts de correus o missatges sospitosos</li>
      <li>Verifica sempre HTTPS + cadenat al navegador (i evita certificats dubtosos)</li>
      <li>Fes còpies de seguretat regulars (regla 3-2-1: 3 còpies, 2 mitjans, 1 fora de línia)</li>
      <li>Evita Wi-Fi públiques per a operacions sensibles (o usa VPN de confiança)</li>
      <li>Antivirus / EDR actualitzat (no només Windows Defender si fas tasques arriscades)</li>
      <li>Desactiva macros en documents Office per defecte</li>
      <li>Educa't contínuament — el factor humà és el vector d'atac més explotat</li>
    </ol>

    <h2 class="warning-title">⚠️ AVÍS LEGAL IMPORTANT</h2>
    <p class="small">
      Projecte exclusivament educatiu i acadèmic.<br>
      No promou ni facilita activitats il·legals de cap tipus.<br>
      No es recullen dades personals reals més enllà del necessari per a la simulació.<br>
      L'ús indegut d'aquesta plataforma per a finalitats no educatives està prohibit.
    </p>

    <h2 style="margin-top: 3rem;">Recursos recomanats per seguir aprenent</h2>
    <p class="small" style="margin-top: 0.8rem; line-height: 1.8;">
      → <a href="https://www.incibe.es/">INCIBE – Institut Nacional de Ciberseguretat</a><br>
      → <a href="https://www.termcat.cat/ca/diccionaris-en-linia/239">Terminologia ciberseguretat (Termcat)</a><br>
      → <a href="https://ca.wikipedia.org/wiki/Atac_de_denegaci%C3%B3_de_servei">Viquipèdia – Atac de denegació de servei</a><br>
      → Plataformes pràctiques: TryHackMe, HackTheBox, OverTheWire, CTFtime<br>
      → Cursos gratuïts: Cisco Networking Academy, Google Cybersecurity Certificate, INCIBE Formació
    </p>

  </div>

  <script>
    // Esperar a que el DOM esté cargado
    document.addEventListener('DOMContentLoaded', function() {
      // Matrix rain
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
    const columns = canvas.width / fontSize;
    const drops = new Array(Math.floor(columns)).fill(1);

    function drawMatrix() {
      ctx.fillStyle = 'rgba(0, 0, 0, 0.05)';
      ctx.fillRect(0, 0, canvas.width, canvas.height);

      ctx.fillStyle = '#0f0';
      ctx.font = fontSize + 'px Fira Code, monospace';

      for (let i = 0; i < drops.length; i++) {
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


<?php
// Incluir configuración global
require_once __DIR__ . '/config.php';

// Comprovar si l'usuari està loguejat
if (!isset($_SESSION['user_id'])) {
    redirect('login.php');
    exit;
}

// Obtenir nom d'usuari
$username = htmlspecialchars($_SESSION['username'] ?? 'Usuari desconegut', ENT_QUOTES, 'UTF-8');
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
            <strong>🛡️ Panell de Control</strong>
            <div style="display: flex; gap: 20px; align-items: center;">
                <span style="background: rgba(0,255,0,0.1); padding: 8px 12px; border-radius: 4px; border-left: 3px solid #0f0;">Connectat com: <strong><?= $username ?></strong></span>
                <a href="index.php" style="background: linear-gradient(135deg, rgba(0,255,0,0.2), rgba(0,255,0,0.1)); border: 2px solid #0f0; padding: 8px 12px; border-radius: 4px; color: #0f0; transition: all 0.3s; box-shadow: 0 0 10px rgba(0,255,0,0.2);" onmouseover="this.style.boxShadow='0 0 20px rgba(0,255,0,0.5), 0 0 30px rgba(0,255,0,0.3)'" onmouseout="this.style.boxShadow='0 0 10px rgba(0,255,0,0.2)'">← Torna al Inici</a>
                <a href="logout.php" style="transition: all 0.3s;" onmouseover="this.style.textShadow='0 0 10px #f44, 0 0 20px #f44'" onmouseout="this.style.textShadow=''">Tancar Sessió</a>
            </div>
        </div>

        <div class="logo">
            <h1 class="glitch" data-text="TERMINAL DE CONTROL">TERMINAL DE CONTROL</h1>
            <p class="subtitle">Eines educatives i simulacions de seguretat</p>
        </div>

        <div class="services">
            <div class="service">
                <h3>🚀 Simulació DDoS Volumètric</h3>
                <p class="small">UDP / ICMP Flood</p>
                <button type="button" class="submit-btn" onclick="openModal('ddos')">Iniciar Simulació</button>
            </div>

            <div class="service">
                <h3>⚔️ Simulador Rate Limiting</h3>
                <p class="small">Control de peticions per IP</p>
                <button type="button" class="submit-btn" onclick="openModal('rate')">Iniciar Simulació</button>
            </div>

            <div class="service">
                <h3>🛡️ WAF Bàsic</h3>
                <p class="small">Detecció de patrons maliciosos</p>
                <button type="button" class="submit-btn" onclick="openModal('waf')">Provar Regles</button>
            </div>

            <div class="service">
                <h3>🔐 Demo Login Segur</h3>
                <p class="small">CSRF + Hashing + Headers</p>
                <button type="button" class="submit-btn" onclick="openModal('login')">Veure Demo</button>
            </div>

            <div class="service">
                <h3>🔍 Port Scanner</h3>
                <p class="small">Escannejar ports oberts</p>
                <button type="button" class="submit-btn" onclick="openModal('scanner')">Iniciar Scan</button>
            </div>

            <div class="service">
                <h3>💾 Brute Force Simulator</h3>
                <p class="small">Atac de força bruta</p>
                <button type="button" class="submit-btn" onclick="openModal('brute')">Simular Atac</button>
            </div>

            <div class="service">
                <h3>📊 Packet Sniffer</h3>
                <p class="small">Captura de paquets</p>
                <button type="button" class="submit-btn" onclick="openModal('sniffer')">Capturar Paquets</button>
            </div>

            <div class="service">
                <h3>🌐 DNS Spoofing</h3>
                <p class="small">Simulació d'atac DNS</p>
                <button type="button" class="submit-btn" onclick="openModal('dns')">Iniciar Atac</button>
            </div>
        </div>

        <div style="margin-top: 3rem; padding: 1.5rem; background: rgba(0,255,0,0.08); border: 2px solid #0f0; border-radius: 8px; box-shadow: 0 0 20px rgba(0,255,0,0.2), inset 0 0 15px rgba(0,255,0,0.1);">
            <h2 style="margin-bottom: 1rem; text-align: center;">📈 Estadístiques del Sistema 🔬</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                <div style="padding: 1rem; background: linear-gradient(135deg, rgba(0,255,0,0.1), rgba(0,255,0,0.05)); border: 2px solid rgba(0,255,0,0.4); border-radius: 4px; box-shadow: 0 0 15px rgba(0,255,0,0.15); transition: all 0.3s ease;">
                    <p style="color: #0a0; font-size: 0.9rem; font-weight: 600;">⚡ Peticions/seg</p>
                    <p style="font-size: 1.8rem; color: #0f0; margin: 0.5rem 0; text-shadow: 0 0 10px #0f0;" id="statReqs">2,847</p>
                </div>
                <div style="padding: 1rem; background: linear-gradient(135deg, rgba(0,255,0,0.1), rgba(0,255,0,0.05)); border: 2px solid rgba(0,255,0,0.4); border-radius: 4px; box-shadow: 0 0 15px rgba(0,255,0,0.15); transition: all 0.3s ease;">
                    <p style="color: #0a0; font-size: 0.9rem; font-weight: 600;">🚨 Amenaces Detectades</p>
                    <p style="font-size: 1.8rem; color: #0f0; margin: 0.5rem 0; text-shadow: 0 0 10px #0f0;" id="statThreats">47</p>
                </div>
                <div style="padding: 1rem; background: linear-gradient(135deg, rgba(0,255,0,0.1), rgba(0,255,0,0.05)); border: 2px solid rgba(0,255,0,0.4); border-radius: 4px; box-shadow: 0 0 15px rgba(0,255,0,0.15); transition: all 0.3s ease;">
                    <p style="color: #0a0; font-size: 0.9rem; font-weight: 600;">✅ Uptime</p>
                    <p style="font-size: 1.8rem; color: #0f0; margin: 0.5rem 0; text-shadow: 0 0 10px #0f0;" id="statUptime">99.97%</p>
                </div>
                <div style="padding: 1rem; background: linear-gradient(135deg, rgba(0,255,0,0.1), rgba(0,255,0,0.05)); border: 2px solid rgba(0,255,0,0.4); border-radius: 4px; box-shadow: 0 0 15px rgba(0,255,0,0.15); transition: all 0.3s ease;">
                    <p style="color: #0a0; font-size: 0.9rem; font-weight: 600;">📡 Latència</p>
                    <p style="font-size: 1.8rem; color: #0f0; margin: 0.5rem 0; text-shadow: 0 0 10px #0f0;" id="statLatency">12ms</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal per a simulacions -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle" style="margin: 0; color: #0f0;"></h2>
                <button type="button" class="close-modal" onclick="closeModal()">✕</button>
            </div>
            <div id="modalForm"></div>
            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="button" class="submit-btn" onclick="runSimulation()" style="flex: 1;">EXECUTA L'ATAC</button>
                <button type="button" onclick="closeModal()" style="flex: 1; background: #0f03; border: 1px solid #0f0; color: #0f0; padding: 14px; border-radius: 4px; cursor: pointer; font-family: 'Fira Code'; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; transition: all 0.25s;">CANCEL·LA</button>
            </div>
            <div id="modalResult" style="margin-top:30px; display:none;">
                <div style="margin-bottom: 15px;">
                    <p style="color: #0c0; font-size: 0.9rem; margin-bottom: 5px;">PROGRES DE L'EXECUCIÓ</p>
                    <div class="progress-bar" style="height:12px; background:#0f02; border-radius:6px; overflow:hidden;">
                        <div id="progress" class="progress-fill" style="height:100%; width:0%;"></div>
                    </div>
                </div>
                <p style="color: #0c0; font-size: 0.9rem; margin-bottom: 5px;">LOGS DE SIMULACIÓ</p>
                <div id="logs" class="log-container"></div>
            </div>
        </div>
    </div>

    <script>
        let currentTool = '';

        function openModal(tool) {
            currentTool = tool;
            const modal = document.getElementById('modal');
            const title = document.getElementById('modalTitle');
            const form = document.getElementById('modalForm');
            const result = document.getElementById('modalResult');
            result.style.display = 'none';

            let html = '';

            if (tool === 'ddos') {
                title.textContent = '🚀 Simulació DDoS Volumètric';
                html = `<div class="form-group"><label>[ IP DESTÍ ]</label><input type="text" id="ip" value="10.0.0.50" placeholder="INTRODUEIX IP..."></div><div class="form-group"><label>[ PORT ]</label><input type="number" id="port" value="80"></div><div class="form-group"><label>[ TIPUS DE FLOOD ]</label><select id="floodType"><option value="udp">UDP Flood</option><option value="icmp">ICMP Flood</option><option value="syn">SYN Flood</option></select></div>`;
            } else if (tool === 'rate') {
                title.textContent = '⚔️ Simulador Rate Limiting';
                html = `<div class="form-group"><label>[ IP CLIENT ]</label><input type="text" id="ip" value="172.16.0.45"></div><div class="form-group"><label>[ PETICIONS/SEGON ]</label><input type="number" id="rps" value="150" min="10" max="1000"></div>`;
            } else if (tool === 'waf') {
                title.textContent = '🛡️ WAF Bàsic';
                html = `<div class="form-group"><label>[ TIPUS D'ATAC ]</label><select id="attack"><option value="sqli">SQL Injection</option><option value="xss">XSS</option><option value="csrf">CSRF</option></select></div><div class="form-group"><label>[ PARÀMETRE D'ENTRADA ]</label><input type="text" id="param" value="user_input" placeholder="nom del paràmetre"></div>`;
            } else if (tool === 'login') {
                title.textContent = '🔐 Demo Login Segur';
                html = `<div class="form-group"><label>[ NOM D'USUARI ]</label><input type="text" id="user" value="admin" placeholder="usuari"></div><div class="form-group"><label>[ CONTRASENYA ]</label><input type="password" id="pass" value="password123" placeholder="contrasenya"></div>`;
            } else if (tool === 'scanner') {
                title.textContent = '🔍 Port Scanner';
                html = `<div class="form-group"><label>[ IP DESTÍ ]</label><input type="text" id="ip" value="192.168.1.1" placeholder="INTRODUEIX IP..."></div><div class="form-group"><label>[ RANG DE PORTS ]</label><div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;"><input type="number" id="portStart" value="1" placeholder="Port inicial" min="1" max="65535"><input type="number" id="portEnd" value="100" placeholder="Port final" min="1" max="65535"></div></div>`;
            } else if (tool === 'brute') {
                title.textContent = '💾 Brute Force Simulator';
                html = `<div class="form-group"><label>[ OBJECTIU ]</label><input type="text" id="target" value="admin@example.com" placeholder="usuari/email"></div><div class="form-group"><label>[ DICCIONARI ]</label><select id="dict"><option value="common">Contraseyes Comunes</option><option value="weak">Contraseyes Dèbils</option><option value="rockyou">RockYou.txt</option></select></div>`;
            } else if (tool === 'sniffer') {
                title.textContent = '📊 Packet Sniffer';
                html = `<div class="form-group"><label>[ INTERFÍCIE DE XARXA ]</label><select id="iface"><option value="eth0">eth0</option><option value="wlan0">wlan0</option><option value="any">Qualsevol</option></select></div><div class="form-group"><label>[ NOMBRE DE PAQUETS ]</label><input type="number" id="packets" value="50" min="10" max="1000"></div>`;
            } else if (tool === 'dns') {
                title.textContent = '🌐 DNS Spoofing';
                html = `<div class="form-group"><label>[ DOMINI OBJECTIU ]</label><input type="text" id="domain" value="example.com" placeholder="exemple.com"></div><div class="form-group"><label>[ IP FALSA ]</label><input type="text" id="fakeIP" value="192.168.1.100" placeholder="IP falsa"></div>`;
            }

            form.innerHTML = html;
            modal.classList.add('active');
        }

        function closeModal() {
            document.getElementById('modal').classList.remove('active');
        }

        function runSimulation() {
            const result = document.getElementById('modalResult');
            const progress = document.getElementById('progress');
            const logs = document.getElementById('logs');
            result.style.display = 'block';
            progress.style.width = '0%';
            logs.innerHTML = '';

            setTimeout(() => {
                progress.style.width = '100%';
            }, 100);

            let simulationLogs = [];

            if (currentTool === 'ddos') {
                const ip = document.getElementById('ip')?.value || '10.0.0.50';
                const port = document.getElementById('port')?.value || '80';
                const floodType = document.getElementById('floodType')?.value || 'udp';
                simulationLogs = [
                    '[INIT] Iniciant generador de flood ' + floodType.toUpperCase(),
                    '[SOCKET] Creant sockets raw... ✓',
                    '[SPOOF] Suplantant IP origen',
                    '[SEND] → ' + ip + ':' + port + ' | 1000 paquets/seg',
                    '[SEND] → ' + ip + ':' + port + ' | 2500 paquets/seg ⚠️',
                    '[SEND] → ' + ip + ':' + port + ' | 5200 paquets/seg 🔴',
                    '[DETECT] Detectat bloqueig de firewall',
                    '[MITIGATION] Aplicant contraatac... ✓',
                    '[COMPLETE] Simulació finalizada en 4.2 segons'
                ];
            } else if (currentTool === 'rate') {
                const ip = document.getElementById('ip')?.value || '172.16.0.45';
                const rps = document.getElementById('rps')?.value || '150';
                simulationLogs = [
                    '[RATE-LIMIT] IP origen: ' + ip,
                    '[TRACKER] Registrant peticions...',
                    '[COUNT] 10 req/seg ✓',
                    '[COUNT] 25 req/seg ✓',
                    '[COUNT] ' + rps + ' req/seg 🔴 LIMITE EXCEDIT',
                    '[BLOCK] IP ' + ip + ' bloqueada per 300 segons',
                    '[CACHE] Afegint a blacklist temporal',
                    '[STATUS] Rate limiting activat ✓'
                ];
            } else if (currentTool === 'waf') {
                const attack = document.getElementById('attack')?.value || 'sqli';
                const param = document.getElementById('param')?.value || 'user_input';
                const xssPayload = attack === 'xss' ? '[SCRIPT]alert()[/SCRIPT]' : "' OR '1'='1";
                simulationLogs = [
                    '[WAF] Analizant petició HTTP...',
                    '[PAYLOAD] Detectat en paràmetre: ' + param,
                    '[PATTERN] Matching contra base de dades de patrons',
                    '[ALERT] Pausa detectada: ' + (attack === 'sqli' ? "' OR '1'='1" : xssPayload),
                    '[SCORE] Puntuació de risc: 9.5/10 🔴',
                    '[ACTION] Bloquejant petició - HTTP 403',
                    '[LOG] Incident registrat en auditoria',
                    '[COMPLETE] ' + (attack === 'sqli' ? 'SQL Injection' : 'XSS') + ' neutralitzat ✓'
                ];
            } else if (currentTool === 'login') {
                simulationLogs = [
                    '[AUTH] Verificant CSRF token → vàlid',
                    '[HASH] Comparant bcrypt SHA-256',
                    '[CHECK] Entrada de contrasenya correcta',
                    '[MFA] Generant token 2FA (OTP)',
                    '[SESSION] Creant cookie de sessió segura',
                    '[HEADER] Content-Security-Policy ✓',
                    '[HEADER] X-Frame-Options: DENY ✓',
                    '[SECURE] HttpOnly + Secure flags activats',
                    '[SUCCESS] Sessió iniciada amb èxit ✓'
                ];
            } else if (currentTool === 'scanner') {
                const ip = document.getElementById('ip')?.value || '192.168.1.1';
                const portStart = parseInt(document.getElementById('portStart')?.value || '1');
                const portEnd = parseInt(document.getElementById('portEnd')?.value || '100');
                simulationLogs = [
                    '[SCANNER] Escaneig de ports en ' + ip,
                    '[TCP] Iniciant conexions SYN...',
                    '[PORT] ' + (portStart + 21) + ' → SSH (OpenSSH 7.4) ✓',
                    '[PORT] ' + (portStart + 25) + ' → SMTP (Postfix) ✓',
                    '[PORT] ' + (portStart + 53) + ' → DNS (BIND 9.11) ✓',
                    '[PORT] ' + (portStart + 80) + ' → HTTP (Apache 2.4) ✓',
                    '[PORT] ' + (portStart + 443) + ' → HTTPS ✓',
                    '[COMPLETE] ' + (portEnd - portStart) + ' ports escanejats | 5 oberts detectats'
                ];
            } else if (currentTool === 'brute') {
                const target = document.getElementById('target')?.value || 'admin@example.com';
                simulationLogs = [
                    '[BRUTE] Objectiu: ' + target,
                    '[ATTEMPT] password → FAIL',
                    '[ATTEMPT] 123456 → FAIL',
                    '[ATTEMPT] password123 → FAIL',
                    '[ATTEMPT] admin → FAIL',
                    '[ATTEMPT] qwerty → FAIL',
                    '[LOCKOUT] Compte bloquejat després de 5 intents',
                    '[ALERT] IP registrada en sistema de defensa',
                    '[LOG] Intent de força bruta detectat i blocat ✓'
                ];
            } else if (currentTool === 'sniffer') {
                const packets = document.getElementById('packets')?.value || '50';
                simulationLogs = [
                    '[SNIFFER] Capturant paquets...',
                    '[PKT] 192.168.1.100:54321 → 8.8.8.8:53 | DNS Query',
                    '[PKT] 10.0.0.50:80 → 192.168.1.1:55000 | HTTP GET',
                    '[PKT] 192.168.1.150:443 → 104.21.35.82:443 | TLS 1.3',
                    '[PKT] 172.16.0.1:22 → 192.168.1.100:40000 | SSH',
                    '[PAYLOAD] Detectat: Cookie=ABC123XYZ789',
                    '[PARSE] ' + packets + ' paquets capturats',
                    '[EXPORT] Guardant en pcap format',
                    '[COMPLETE] Captura finalizada ✓'
                ];
            } else if (currentTool === 'dns') {
                const domain = document.getElementById('domain')?.value || 'example.com';
                const fakeIP = document.getElementById('fakeIP')?.value || '192.168.1.100';
                simulationLogs = [
                    '[DNS] Spoofing actiu per a: ' + domain,
                    '[ARP] Suplantant ARP en xarxa local',
                    '[INTERCEPT] Interceptant queries DNS',
                    '[RESPONSE] → ' + domain + ' = ' + fakeIP,
                    '[REDIRECT] Redirigint tràfic a IP falsa',
                    '[MITM] Executant Man-in-the-Middle attack',
                    '[PHISHING] Servint pàgina falsa...',
                    '[LOG] 47 usuaris redirigits correctament',
                    '[CLEANUP] Restaurant taula ARP original'
                ];
            }

            simulationLogs.forEach((line, index) => {
                setTimeout(() => {
                    const div = document.createElement('div');
                    div.className = 'log-line';
                    div.style.color = line.includes('✓') ? '#0f0' : line.includes('🔴') || line.includes('FAIL') ? '#f44' : '#0a0';
                    div.textContent = line;
                    logs.appendChild(div);
                    logs.scrollTop = logs.scrollHeight;
                }, 300 * (index + 1));
            });

            updateStats();
        }

        function updateStats() {
            const stats = [
                { id: 'statReqs', min: 1200, max: 5000 },
                { id: 'statThreats', min: 8, max: 156 },
                { id: 'statLatency', min: 5, max: 85, suffix: 'ms' }
            ];

            stats.forEach(stat => {
                const current = Math.floor(Math.random() * (stat.max - stat.min) + stat.min);
                document.getElementById(stat.id).textContent = current.toLocaleString() + (stat.suffix ? stat.suffix : '');
            });
        }

        // Matrix rain - VERSIÓN ULTRA LIGERA (solo cada 150ms)
        document.addEventListener('DOMContentLoaded', function() {
            const canvas = document.getElementById('matrix');
            if (!canvas) {
                console.log('✓ Canvas deshabilitado para mejor rendimiento');
                return;
            }
            
            const ctx = canvas.getContext('2d');
            canvas.height = window.innerHeight;
            canvas.width = window.innerWidth;

            const chars = '01█▓▒░';
            const fontSize = 18;
            const columns = Math.floor(canvas.width / fontSize);
            const drops = new Array(columns).fill(1);

            function drawMatrix() {
                ctx.fillStyle = 'rgba(0, 0, 0, 0.1)';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.fillStyle = '#0f0';
                ctx.font = fontSize + 'px "Fira Code", monospace';

                for (let i = 0; i < columns; i++) {
                    if (Math.random() > 0.9) {
                        const char = chars.charAt(Math.floor(Math.random() * chars.length));
                        ctx.fillText(char, i * fontSize, drops[i] * fontSize);
                    }
                    if (drops[i] > canvas.height / fontSize && Math.random() > 0.95) {
                        drops[i] = 0;
                    }
                    drops[i] += 0.5;
                }
            }

            setInterval(drawMatrix, 150);
            console.log('✓ Canvas ultra-ligero inicializado');
        });
    </script>

</body>
</html>

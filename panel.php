<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
$username = htmlspecialchars($_SESSION['username'] ?? 'Usuari');
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>CyberEdu Tactical OS</title>
    
    <link rel="stylesheet" href="styles.css?v=<?php echo time(); ?>"> 
    
    <script type="text/javascript" src="https://unpkg.com/vis-network/dist/vis-network.min.js"></script>
</head>
<body>

    <nav class="navbar">
        <div style="display: flex; align-items: center;">
            <strong class="brand-title">[ CYBEREDU_OS ]</strong>
            <a href="index.php" class="nav-btn btn-home">⌂ INICI</a>
        </div>
        <div style="display: flex; align-items: center;">
            <div class="agent-tag"><span class="dot"></span> AGENT: <?= strtoupper($username) ?></div>
            <a href="logout.php" class="nav-btn btn-logout">SORTIR [X]</a>
        </div>
    </nav>

    <div class="main-container">
        <div class="grid">
            <div class="tool-card" onclick="openModal('scanner')"><h3>NMAP PRO</h3></div>
            <div class="tool-card" onclick="openModal('audit')"><h3>WEB AUDIT</h3></div>
            <div class="tool-card" onclick="openModal('breach')"><h3>MAIL OSINT</h3></div>
            <div class="tool-card" onclick="openModal('geo')"><h3>GEO TRACK</h3></div>
            <div class="tool-card" onclick="openModal('dns')"><h3>DNS MAP</h3></div>
            <div class="tool-card" onclick="openModal('password')"><h3>PASS TEST</h3></div>
        </div>

        <div id="outputContainer" class="output-container">
            <div class="controls">
                <button onclick="stopProcess()" id="stopBtn" class="nav-btn btn-stop">ATURAR</button>
                <div id="viewSwitch" style="display:none;">
                    <button onclick="switchView('text')" class="nav-btn">TERMINAL</button>
                    <button onclick="switchView('graph')" class="nav-btn btn-graph">MAPA XARXA</button>
                </div>
                <button onclick="downloadReport()" id="dlBtn" class="nav-btn" style="display:none;">DESCARREGAR TXT</button>
            </div>
            
            <div id="logArea" class="log-area"><div id="logContent"></div></div>
            <div id="networkGraph"></div>
        </div>
    </div>

    <div id="modal" class="modal">
        <div class="modal-box">
            <h2 id="modalTitle"></h2>
            <input type="text" id="target" placeholder="Escriu l'objectiu...">
            <div id="portRange" class="range-inputs" style="display:none; gap:10px;">
                <input type="number" id="pStart" value="1" placeholder="Inici">
                <input type="number" id="pEnd" value="100" placeholder="Final">
            </div>
            <button class="nav-btn btn-execute" onclick="runTool()" style="width:100%; margin-top:20px;">EXECUTAR MISSIÓ</button>
            <p onclick="closeModal()" class="btn-cancel" style="cursor:pointer; text-align:center; margin-top:15px; color:#444;">[ CANCEL·LAR ]</p>
        </div>
    </div>

    <script src="scripts.js?v=<?php echo time(); ?>"></script>
</body>
</html>

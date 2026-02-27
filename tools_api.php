<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Sessió no vàlida']);
    exit;
}

header('Content-Type: application/json');
set_time_limit(300); 

$tool = $_POST['tool'] ?? '';
$target = trim($_POST['target'] ?? ''); // Netegem espais en blanc

// Treure el prefix http:// o https:// si l'usuari el posa, ja que Nmap no el vol
$host = preg_replace('#^https?://#', '', $target);
$host = rtrim($host, '/');

$results = [];

switch ($tool) {
    case 'scanner':
        $results[] = "--- [ AUDITORIA PROFUNDA NMAP (v7.9+) ] ---";
        
        $pStart = intval($_POST['portStart'] ?? 1);
        $pEnd = intval($_POST['portEnd'] ?? 100);
        
        // Intentem resoldre la IP des de PHP per verificar si el domini existeix
        $ip_check = gethostbyname($host);
        
        if ($ip_check === $host && !filter_var($host, FILTER_VALIDATE_IP)) {
            $results[] = "❌ ERROR: No es pot resoldre el domini '$host'.";
            $results[] = "INFO: Comprova que el nom sigui correcte o que el servidor tingui Internet.";
        } else {
            // PARAMETRES CRÍTICS:
            // -Pn: NO fa ping (evita que el servidor ens bloquegi abans de començar)
            // -sV: Detecció de versions
            // -sC: Scripts de seguretat
            // --open: Només mostra ports que responen
            $host_safe = escapeshellarg($host);
            $cmd = "nmap -p $pStart-$pEnd -sV -sC -Pn -T4 --open $host_safe 2>&1";
            
            $results[] = "OBJECTIU: $host ($ip_check)";
            $results[] = "ORDRE: nmap -p $pStart-$pEnd ... $host";
            $results[] = "INFO: Iniciant escaneig silenciós (ignorant PING)...";

            $output = shell_exec($cmd);
            
            if ($output) {
                $lines = explode("\n", $output);
                foreach ($lines as $line) {
                    if (trim($line) == "" || strpos($line, "Starting Nmap") !== false) continue;
                    
                    if (strpos($line, "open") !== false) $results[] = "✅ " . trim($line);
                    elseif (strpos($line, "|") === 0 || strpos($line, "_") === 0) $results[] = "   " . trim($line);
                    else $results[] = trim($line);
                }
            } else {
                $results[] = "❌ ERROR: Nmap no ha retornat dades. Revisa els permisos del sistema.";
            }
        }
        $results[] = "--- [ ESCANEIG FINALITZAT ] ---";
        break;

    case 'audit':
        $results[] = "--- [ AUDITORIA TÈCNICA HTTP ] ---";
        $h = @get_headers("https://" . $host, 1);
        if($h) {
            foreach($h as $key => $value) {
                if(is_array($value)) $value = implode(", ", $value);
                $results[] = "[$key]: $value";
            }
        } else {
            $results[] = "❌ No s'han pogut obtenir les capçaleres.";
        }
        break;

    case 'breach':
        $results[] = "--- [ EMAIL SECURITY OSINT ] ---";
        $domain = (strpos($target, '@') !== false) ? substr(strrchr($target, "@"), 1) : $target;
        $txt = @dns_get_record($domain, DNS_TXT);
        $spf = false;
        if($txt) foreach($txt as $t) if(isset($t['txt']) && str_contains($t['txt'], 'v=spf1')) $spf = $t['txt'];
        $results[] = $spf ? "✅ SPF: $spf" : "❌ ALERTA: Domini sense SPF.";
        break;

    case 'password':
        $results[] = "--- [ SEGURETAT DE CREDENCIALS ] ---";
        $results[] = "Hash SHA-256: " . hash('sha256', $target);
        $results[] = (strlen($target) < 12) ? "⚠️ ALERTA: Massa curta." : "✅ Longitud segura.";
        break;

    case 'geo':
        $ip = gethostbyname($host);
        $geo = json_decode(@file_get_contents("http://ip-api.com/json/$ip"), true);
        if($geo && $geo['status'] == 'success') {
            $results[] = "UBICACIÓ: " . $geo['city'] . ", " . $geo['country'];
            $results[] = "ISP: " . $geo['isp'];
        }
        break;
        
    case 'dns':
        $results[] = "--- [ DNS MAPPER ] ---";
        $recs = @dns_get_record($host, DNS_A + DNS_MX);
        if($recs) foreach($recs as $r) $results[] = "[" . $r['type'] . "] -> " . ($r['ip'] ?? $r['target']);
        break;
}

echo json_encode(['logs' => $results]);

console.log("SISTEMA: scripts.js carregat correctament.");

let currentTool = '';
let lastLogs = [];
let controller;
let network = null;

// Funció per obrir el modal
function openModal(tool) {
    console.log("Obrint mòdul:", tool);
    currentTool = tool;
    const title = document.getElementById('modalTitle');
    const range = document.getElementById('portRange');
    const modal = document.getElementById('modal');
    
    if(title) title.textContent = '> MÒDUL: ' + tool.toUpperCase();
    if(range) range.style.display = (tool === 'scanner') ? 'flex' : 'none';
    if(modal) modal.classList.add('active');
}

// Funció per tancar el modal
function closeModal() { 
    const modal = document.getElementById('modal');
    if(modal) modal.classList.remove('active'); 
}

// Canviar entre Terminal i Gràfic
function switchView(type) {
    const logArea = document.getElementById('logArea');
    const graph = document.getElementById('networkGraph');
    if(logArea) logArea.style.display = (type === 'text') ? 'block' : 'none';
    if(graph) graph.style.display = (type === 'graph') ? 'block' : 'none';
    if(type === 'graph' && network) network.fit(); 
}

// Executar l'eina
async function runTool() {
    const targetInput = document.getElementById('target');
    const target = targetInput ? targetInput.value : '';
    if(!target) {
        alert("Escriu un objectiu vàlid");
        return;
    }
    
    closeModal();
    const outputContainer = document.getElementById('outputContainer');
    const logContent = document.getElementById('logContent');
    const viewSwitch = document.getElementById('viewSwitch');

    if(outputContainer) outputContainer.style.display = 'block';
    if(logContent) logContent.innerHTML = '<div style="color:var(--info)">> Iniciant seqüència...</div>';
    if(viewSwitch) viewSwitch.style.display = 'none';
    switchView('text');

    controller = new AbortController();
    const fd = new FormData();
    fd.append('tool', currentTool);
    fd.append('target', target);
    
    if(currentTool === 'scanner') {
        const pStart = document.getElementById('pStart');
        const pEnd = document.getElementById('pEnd');
        fd.append('portStart', pStart ? pStart.value : '1');
        fd.append('portEnd', pEnd ? pEnd.value : '100');
    }

    try {
        const res = await fetch('tools_api.php', { 
            method: 'POST', 
            body: fd, 
            signal: controller.signal 
        });
        const data = await res.json();
        lastLogs = data.logs || [];
        renderLogs(lastLogs);
        
        if(currentTool === 'scanner') {
            if(viewSwitch) viewSwitch.style.display = 'block';
            generateGraph(target, lastLogs);
        }
    } catch(e) {
        if(logContent) logContent.innerHTML += '<div style="color:var(--alert)">> ERROR: Sessió interrompuda.</div>';
    }
}

// Dibuixar logs a la terminal
function renderLogs(logs) {
    const logContent = document.getElementById('logContent');
    const logArea = document.getElementById('logArea');
    const dlBtn = document.getElementById('dlBtn');
    if(!logContent) return;

    logContent.innerHTML = '';
    logs.forEach((line, i) => {
        setTimeout(() => {
            const div = document.createElement('div');
            if(line.includes('✅')) div.style.color = 'var(--neon)';
            else if(line.includes('❌') || line.includes('ALERTA')) div.style.color = 'var(--alert)';
            else if(line.includes('|') || line.includes('_')) div.style.color = 'var(--info)';
            div.textContent = line;
            logContent.appendChild(div);
            if(logArea) logArea.scrollTop = logArea.scrollHeight;
            if(i === logs.length - 1 && dlBtn) dlBtn.style.display = 'block';
        }, i * 15);
    });
}

// Generar el mapa Vis.js
function generateGraph(target, logs) {
    const nodes = [{ id: 1, label: target, shape: 'diamond', color: '#00ff41', font: {color:'#fff'} }];
    const edges = [];
    let idCount = 2;

    logs.forEach(line => {
        const match = line.match(/(\d+)\/tcp\s+open\s+(.*)/);
        if(match) {
            nodes.push({ id: idCount, label: `PORT ${match[1]}\n${match[2]}`, shape: 'dot', color: '#00d4ff' });
            edges.push({ from: 1, to: idCount });
            idCount++;
        }
    });

    const container = document.getElementById('networkGraph');
    if(network) network.destroy();
    if(container) {
        network = new vis.Network(container, { 
            nodes: new vis.DataSet(nodes), 
            edges: new vis.DataSet(edges) 
        }, {
            physics: { enabled: true, stabilization: true },
            edges: { color: '#444' }
        });
    }
}

function stopProcess() { if(controller) controller.abort(); }

function downloadReport() {
    const blob = new Blob([lastLogs.join('\n')], {type: 'text/plain'});
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = `report_${currentTool}.txt`;
    a.click();
}

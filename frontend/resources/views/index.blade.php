<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Análise de Ações com IA</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* ====== RESET E VARIÁVEIS DE TEMA ====== */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', "Inter", system-ui, -apple-system, sans-serif;
        }

        /* Padrão: LIGHT MODE */
        body {
            --bg-color: #f0f2f5; 
            --text-color: #df1e26;
            --text-input: #2d2d2e;
            --subtext-color: #2d2d2e;
            --card-bg: #ffffff; 
            --input-bg: #e9ecef;
            --accent: #df1e26;
            --button-hover: #feb408;
            --switch-w: 60px;
            --switch-h: 30px;
            --circle-size: 24px;
            
            background-color: var(--bg-color);
            color: var(--text-color);
            display: flex;
            align-items: flex-start; 
            justify-content: center;
            min-height: 100vh;
            padding-top: 50px; 
            padding-bottom: 100px;
            transition: background-color 0.4s ease, color 0.4s ease;
        }

        /* DARK MODE */
        body.dark {
            --bg-color: #1a1b1e;
            --text-color: #2d54a4;
            --subtext-color: #dbd9d9;
            --text-input: #ffffff;
            --card-bg: #292a2d;
            --input-bg: #35363a; 
            --accent: #0d6efd;
            --button-hover: #01ae47;
        }

        /* ====== CONTAINER PRINCIPAL ====== */
        .app-container {
            background-color: transparent; 
            padding: 0 20px;
            width: 100%;
            max-width: 700px; 
            text-align: left;
            position: relative;
            transition: background-color 0.4s ease;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .header h1 {
            font-size: 2em;
            font-weight: 700;
            color: var(--accent); 
        }
        
        .header p {
            color: var(--subtext-color);
            opacity: 1;
            font-size: 1rem;
            margin-top: 5px;
        }

        .header .logo {
            display: block;
            margin: 50px auto 50px; 
            max-width: 250px;    
            height: auto;        
        }

        /* ================================== */
        /* ✅ ESTILO DO LINK DE CURADORIA */
        /* ================================== */
        .curadoria-link {
            margin-top: 25px;
            padding: 10px 15px;
            background-color: var(--input-bg);
            border-radius: 8px;
            display: inline-block;
            transition: background-color 0.3s ease;
        }
        .curadoria-link a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            transition: color 0.3s ease;
        }
        .curadoria-link a:hover {
            text-decoration: underline;
            color: var(--button-hover);
        }
        /* ================================== */

        /* ====== CAIXA DE RESULTADO (Resultado das Análises) ====== */
        .result-box {
            background-color: var(--card-bg);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 20px;
            color: var(--text-input);
            font-size: 0.95rem;
            line-height: 1.6;
            white-space: pre-wrap;
            word-wrap: break-word;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            opacity: 0;
            transform: translateY(10px);
            transition: all 0.4s ease-out;
            border: 1px solid #eee; /* Borda sutil */
        }
        
        body.dark .result-box {
            border: 1px solid #35363a;
        }
        
        .result-box.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .result-box .user-prompt {
            font-weight: bold;
            margin-bottom: 10px;
            color: var(--accent);
        }

        .result-box .error-message {
            color: var(--accent); /* Usando a cor de destaque para consistência */
        }

        .result-box .wait-message {
            color: #ff9800;
            background-color: rgba(255, 152, 0, 0.1);
            padding: 10px;
            border-radius: 8px;
            margin-top: 10px;
        }
        
        body.dark .result-box .error-message {
            color: #dc3545;
        }
        body.dark .result-box .wait-message {
            background-color: rgba(255, 152, 0, 0.2);
        }

        .loading-dots {
            display: inline-block;
            animation: blink 1.4s infinite;
        }

        @keyframes blink {
            0%, 80%, 100% { opacity: 0; }
            40% { opacity: 1; }
        }

        /* ====== BARRA DE ENTRADA ====== */
        .input-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            display: flex;
            justify-content: center;
            padding: 20px;
            background-color: var(--bg-color);
            box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.1);
            z-index: 100;
            transition: background-color 0.4s ease;
        }

        .input-group {
            display: flex;
            width: 100%;
            max-width: 680px;
            background-color: var(--input-bg);
            border-radius: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: 1px solid var(--input-bg); 
            transition: all 0.3s;
        }

        .center-input {
            margin: 0 auto;
            margin-top: 30px;
            position: relative;
        }
        
        .input-group:focus-within {
            border-color: var(--accent);
            box-shadow: 0 0 0 1px var(--accent);
        }

        input {
            flex-grow: 1;
            padding: 12px 20px;
            border: none;
            background: transparent;
            color: var(--text-input);
            font-size: 1rem;
            outline: none;
        }

        input::placeholder {
            color: var(--text-input);
            opacity: 0.5;
        }

        button {
            background-color: var(--accent);
            color: white;
            border: none;
            padding: 12px 20px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
            min-width: 100px;
            border-radius: 0 25px 25px 0;
        }

        button:hover:not(:disabled) {
            background-color: var(--button-hover);
        }

        button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* ====== NOVO ESTILO TOGGLE SWITCH (Copiado do Painel de Curadoria) ====== */
        .theme-switch {
            width: var(--switch-w);
            height: var(--switch-h);
            position: fixed; 
            top: 20px;
            right: 20px;
            background: #d6d6d6;
            border-radius: 999px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);
            cursor: pointer;
            transition: background-color 0.3s ease;
            z-index: 200;
            
            /* Novo alinhamento interno para os ícones */
            display: flex;
            align-items: center;
            padding: 0 3px;
        }

        .theme-switch .switch-circle {
            position: absolute;
            top: 3px; /* Ajustado para 3px para caber no 30px de altura */
            left: 3px;
            width: var(--circle-size);
            height: var(--circle-size);
            background: white;
            border-radius: 50%;
            z-index: 2;
            transition: left 0.35s cubic-bezier(.4,0,.2,1), background 0.25s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        
        /* Estilos dos Ícones Font Awesome */
        .theme-switch i {
            font-size: 16px;
            position: absolute;
            z-index: 3;
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
        
        /* Posição e Cor dos Ícones no Light Mode */
        .theme-switch .fa-sun {
            left: 8px;
            color: #ffc107; 
            opacity: 1;
        }
        .theme-switch .fa-moon {
            right: 8px;
            color: var(--text-color-primary);
            opacity: 0;
            transform: scale(0.8);
        }

        /* Posição e Cor dos Ícones no Dark Mode */
        body.dark .theme-switch {
            background: #3c4043;
        }
        
        body.dark .theme-switch .switch-circle {
            left: calc(100% - var(--circle-size) - 3px);
            background: var(--accent); 
        }
        
        body.dark .theme-switch .fa-sun {
            opacity: 0;
            transform: scale(0.8);
        }
        
        body.dark .theme-switch .fa-moon {
            opacity: 1;
            transform: scale(1);
            color: #ffffff;
        }
    </style>
</head>

<body>
    <div class="theme-switch" id="themeToggle" role="button" tabindex="0">
        <i class="fas fa-sun"></i>
        <i class="fas fa-moon"></i>
        <div class="switch-circle"></div>
    </div>

    <div class="app-container">
        <div class="header">
            <div>
                <img src="{{ asset('img/logo_jc.png') }}" alt="Logo JC" class="logo" />
            </div>
            
            <h1>Análise de Ações com IA</h1>
            <p>Insira o símbolo de uma ação abaixo para iniciar a análise.</p>

            <div class="curadoria-link">
                <a href="{{ route('curadoria.index') }}"><i class="fas fa-rocket"></i> Ir para o Painel de Curadoria</a>
            </div>

            <div class="input-group center-input">
                <input id="stockInput" type="text" placeholder="Ex: PETR4, VALE3, MGLU3...">
                <button id="analyzeBtn">Analisar</button>
            </div>

        </div>

        <div id="resultsContainer"></div>
    </div>

    <script>
    const body = document.body;
    const themeToggle = document.getElementById('themeToggle');
    const resultsContainer = document.getElementById('resultsContainer');
    const analyzeBtn = document.getElementById('analyzeBtn');
    const stockInput = document.getElementById('stockInput');

    // === TEMA ===
    const isSystemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const savedTheme = localStorage.getItem('theme');

    const applyTheme = (theme) => {
        if (theme === 'dark') {
            body.classList.add('dark');
        } else {
            body.classList.remove('dark');
        }
    };
    
    if (savedTheme) {
        applyTheme(savedTheme);
    } else if (isSystemDark) {
        applyTheme('dark');
    } else {
        applyTheme('light');
    }

    function toggleTheme() {
        const newTheme = body.classList.contains('dark') ? 'light' : 'dark';
        applyTheme(newTheme);
        localStorage.setItem('theme', newTheme);
    }
    
    themeToggle.addEventListener('click', toggleTheme);
    themeToggle.addEventListener('keydown', e => {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            toggleTheme();
        }
    });

    // === CRIAR CAIXA DE RESULTADO ===
    function createResultBox(symbol, content, isLoading = false) {
        const newBox = document.createElement('div');
        newBox.className = 'result-box';
        
        const userPrompt = document.createElement('p');
        userPrompt.className = 'user-prompt';
        userPrompt.textContent = `Você: ${symbol}`;
        newBox.appendChild(userPrompt);
        
        const iaResponse = document.createElement('div');
        iaResponse.innerHTML = content;
        newBox.appendChild(iaResponse);

        resultsContainer.appendChild(newBox);
        newBox.offsetWidth; 
        newBox.classList.add('visible');
        
        window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
        
        return newBox;
    }

    // === ANÁLISE DE AÇÃO (CONECTA COM SUA API) ===
    async function analyzeStock() {
        const symbol = stockInput.value.trim().toUpperCase();
        
        if (!symbol) {
            createResultBox("Aviso", "<span class='error-message'>⚠️ Por favor, insira o código de uma ação para iniciar a análise.</span>");
            return;
        }

        const loadingBox = createResultBox(symbol, `🔍 Analisando ${symbol}<span class="loading-dots">...</span><br><small>Isso pode levar até 60 segundos</small>`);

        analyzeBtn.disabled = true; 
        stockInput.value = '';

        try {
            const response = await fetch(`/analyze/${symbol}`);
            const data = await response.json();

            if (response.ok) {
                let formattedMessage = data.message || JSON.stringify(data, null, 2);
                
                formattedMessage = formattedMessage
                    .replace(/================================================/g, '<hr style="border: 1px dashed #ccc; margin: 15px 0;">')
                    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                    .replace(/\n/g, '<br>');
                
                loadingBox.querySelector('div').innerHTML = formattedMessage;
                
            } else if (response.status === 429) {
                const waitSeconds = data.wait_seconds || 30;
                loadingBox.querySelector('div').innerHTML = `
                    <div class="wait-message">
                        ⏱️ ${data.error || data.message}<br><br>
                        <strong>Aguarde ${waitSeconds} segundos antes de tentar novamente.</strong>
                    </div>
                `;
            } else {
                const errorMessage = data.error || data.message || 'Erro desconhecido';
                loadingBox.querySelector('div').innerHTML = `<span class="error-message">❌ ERRO: ${errorMessage}</span>`;
            }

        } catch (error) {
            loadingBox.querySelector('div').innerHTML = `<span class="error-message">❌ Erro de conexão: Não foi possível conectar com a API. Verifique se o servidor está rodando.</span>`;
            console.error('Erro:', error);
        } finally {
            analyzeBtn.disabled = false; 
        }
    }

    // === EVENT LISTENERS ===
    analyzeBtn.addEventListener('click', analyzeStock);
    stockInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !analyzeBtn.disabled) {
            analyzeStock();
        }
    });
    </script>
</body>
</html>
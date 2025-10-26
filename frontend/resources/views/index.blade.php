<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Análise de Ações com IA</title>
  <style>
    /* ====== RESET E VARIÁVEIS DE TEMA ====== */
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: "Inter", system-ui, -apple-system, sans-serif;
    }

    /* Padrão: TEMA CLARO */
    body {
      --bg-color: #f0f2f5; 
      --text-color: #222;
      --card-bg: #ffffff; 
      --input-bg: #e9ecef;
      --accent: #007bff;
      --button-hover: #0062cc;
      --toggle-bg: #d6d6d6;
      --toggle-icon-light: #222; 
      --toggle-icon-dark: #fff;
      
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

    /* TEMA ESCURO */
    body.dark {
      --bg-color: #1a1b1e;
      --text-color: #e3e3e3;
      --card-bg: #292a2d;
      --input-bg: #35363a; 
      --accent: #0d6efd;
      --button-hover: #0a58ca;
      --toggle-bg: #3c4043;
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
      font-weight: 600;
      color: var(--text-color);
    }
    
    .header p {
      color: var(--text-color);
      opacity: 0.7;
      font-size: 1rem;
      margin-top: 5px;
    }

    /* ====== CAIXA DE RESULTADO ====== */
    .result-box {
      background-color: var(--card-bg);
      border-radius: 12px;
      padding: 1.5rem;
      margin-bottom: 20px;
      color: var(--text-color);
      font-size: 0.95rem;
      line-height: 1.6;
      white-space: pre-wrap;
      word-wrap: break-word;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      opacity: 0;
      transform: translateY(10px);
      transition: all 0.4s ease-out;
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
      color: #dc3545;
    }

    .result-box .wait-message {
      color: #ff9800;
      background-color: rgba(255, 152, 0, 0.1);
      padding: 10px;
      border-radius: 8px;
      margin-top: 10px;
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
    
    .input-group:focus-within {
        border-color: var(--accent);
        box-shadow: 0 0 0 1px var(--accent);
    }

    input {
      flex-grow: 1;
      padding: 12px 20px;
      border: none;
      background: transparent;
      color: var(--text-color);
      font-size: 1rem;
      outline: none;
    }

    input::placeholder {
      color: var(--text-color);
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

    /* ====== TOGGLE SWITCH ====== */
    .theme-switch {
      --switch-w: 64px;
      --switch-h: 34px;
      --circle-size: 26px;
      width: var(--switch-w);
      height: var(--switch-h);
      position: fixed; 
      top: 20px;
      right: 20px;
      background: var(--toggle-bg);
      border-radius: 999px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);
      cursor: pointer;
      transition: background-color 0.3s ease;
      z-index: 200;
    }

    .theme-switch .icon {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      width: 16px;
      height: 16px;
      fill: var(--toggle-icon-light); 
      z-index: 3;
      pointer-events: none;
      transition: fill 0.25s ease, opacity 0.25s ease;
    }

    .theme-switch .moon { left: 8px; }
    .theme-switch .sun  { right: 8px; }

    .theme-switch .switch-circle {
      position: absolute;
      top: 4px;
      left: 4px;
      width: var(--circle-size);
      height: var(--circle-size);
      background: white;
      border-radius: 50%;
      z-index: 2;
      transition: left 0.35s cubic-bezier(.4,0,.2,1), background 0.25s ease;
      box-shadow: 0 3px 8px rgba(0, 0, 0, 0.3);
    }

    body:not(.dark) .theme-switch .moon { opacity: 1; fill: var(--toggle-icon-light); }
    body:not(.dark) .theme-switch .sun  { opacity: 0.5; }
    
    body.dark .theme-switch .switch-circle {
      left: calc(100% - var(--circle-size) - 4px);
      background: var(--accent);
    }
    body.dark .theme-switch .moon { opacity: 0.5; }
    body.dark .theme-switch .sun  { opacity: 1; fill: var(--toggle-icon-dark); }
  </style>
</head>

<body>
  <div class="theme-switch" id="themeToggle" role="button" tabindex="0">
    <svg class="icon moon" viewBox="0 0 24 24"><path d="M21.64 13a9 9 0 01-9.64 9 9 9 0 010-18 9.13 9.13 0 012.84.45 7 7 0 006.8 8.55z"/></svg>
    <svg class="icon sun" viewBox="0 0 24 24"><path d="M12 18a6 6 0 100-12 6 6 0 000 12zM12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
    <div class="switch-circle"></div>
  </div>

  <div class="app-container">
    <div class="header">
        <h1>Análise de Ações com IA</h1>
        <p>Insira o símbolo de uma ação abaixo para iniciar a análise.</p>
    </div>

    <div id="resultsContainer"></div>
  </div>

  <div class="input-bar">
    <div class="input-group">
        <input id="stockInput" type="text" placeholder="Ex: PETR4, VALE3, MGLU3..." />
        <button id="analyzeBtn">Analisar</button>
    </div>
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

    if (savedTheme === 'dark' || (savedTheme === null && isSystemDark)) {
        body.classList.add('dark');
    }

    function toggleTheme() {
      body.classList.toggle('dark');
      localStorage.setItem('theme', body.classList.contains('dark') ? 'dark' : 'light');
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

      // Cria caixa de loading
      const loadingBox = createResultBox(symbol, `🔍 Analisando ${symbol}<span class="loading-dots">...</span><br><small>Isso pode levar até 60 segundos</small>`);

      analyzeBtn.disabled = true; 
      stockInput.value = '';

      try {
        // ✅ CHAMADA REAL PARA SUA API
        const response = await fetch(`/analyze/${symbol}`);
        const data = await response.json();

        if (response.ok) {
          // Sucesso - formata a resposta
          let formattedMessage = data.message || JSON.stringify(data, null, 2);
          
          // Adiciona formatação básica
          formattedMessage = formattedMessage
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>') // Negrito
            .replace(/\n/g, '<br>'); // Quebras de linha
          
          loadingBox.querySelector('div').innerHTML = formattedMessage;
          
        } else if (response.status === 429) {
          // Rate limit
          const waitSeconds = data.wait_seconds || 30;
          loadingBox.querySelector('div').innerHTML = `
            <div class="wait-message">
              ⏱️ ${data.error || data.message}<br><br>
              <strong>Aguarde ${waitSeconds} segundos antes de tentar novamente.</strong>
            </div>
          `;
        } else {
          // Erro
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
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Análise de Ações - IA</title>
    <style>
        body { font-family: sans-serif; display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100vh; margin: 0; background-color: #f0f2f5; color: #333; }
        .container { background-color: #fff; padding: 2em; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); text-align: center; }
        h1 { color: #2a2a72; }
        input[type="text"] { padding: 10px; margin: 1em 0; border: 1px solid #ccc; border-radius: 4px; width: 80%; max-width: 300px; }
        button { background-color: #007bff; color: white; padding: 12px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; transition: background-color 0.3s; }
        button:hover { background-color: #0056b3; }
        #result { margin-top: 2em; padding: 1.5em; background-color: #e9ecef; border-radius: 8px; text-align: left; white-space: pre-wrap; word-wrap: break-word; }
        .loading { display: none; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Análise de Ações com IA</h1>
        <p>Insira o símbolo de uma ação para obter uma análise.</p>
        <input type="text" id="symbolInput" placeholder="Ex: PETR4 ou VALE3">
        <button onclick="analyzeStock()">Analisar</button>
        <p class="loading" id="loadingMessage">Analisando... Aguarde.</p>
        <div id="result"></div>
    </div>

    <script>
        async function analyzeStock() {
            const symbol = document.getElementById('symbolInput').value.trim();
            const resultDiv = document.getElementById('result');
            const loadingMessage = document.getElementById('loadingMessage');

            if (!symbol) {
                alert('Por favor, insira o símbolo de uma ação.');
                return;
            }

            // Limpa a área de resultado e mostra a mensagem de carregamento
            resultDiv.innerText = '';
            loadingMessage.style.display = 'block';

            try {
                // Chama a rota do Laravel que se comunica com o backend
                const response = await fetch(`/analyze/${symbol}`);
                const data = await response.json();

                // NOVO CÓDIGO DO JAVASCRIPT
                if (response.ok) {
                    // Se for sucesso, exibe a propriedade 'message' ou a resposta inteira
                    resultDiv.innerText = data.message || JSON.stringify(data, null, 2);
                } else {
                    // Se for erro, exibe a propriedade 'error' ou a resposta inteira formatada
                    const errorMessage = data.error || data.message || JSON.stringify(data, null, 2);
                    resultDiv.innerText = 'ERRO DE RESPOSTA: ' + errorMessage;
                }
            } catch (error) {
                resultDiv.innerText = 'Ocorreu um erro ao conectar com a API. Verifique se os servidores estão rodando.';
            } finally {
                loadingMessage.style.display = 'none';
            }
        }
    </script>
</body>
</html>
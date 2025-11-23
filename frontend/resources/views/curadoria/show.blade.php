<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revisar Análise: {{ $analysis->ticker }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* ====== VARIÁVEIS E ESTILOS GERAIS ====== */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }
        
        /* Paleta Light Mode (Padrão) */
        body {
            --bg-color: #f0f2f5; 
            --text-color-primary: #2d2d2e;
            --card-bg: #ffffff; 
            --accent: #df1e26; 
            --accent-hover: #feb408; 
            --danger: #dc3545;
            --success: #28a745;
            --warning: #ffc107;
            --shadow-light: rgba(0, 0, 0, 0.08);
            --border-color: #eee;

            background-color: var(--bg-color);
            color: var(--text-color-primary);
            padding: 50px 20px;
            display: flex;
            justify-content: center;
            transition: all 0.4s ease;
        }

        /* Paleta Dark Mode */
        body.dark {
            --bg-color: #1a1b1e;
            --text-color-primary: #dbd9d9;
            --card-bg: #292a2d;
            --accent: #0d6efd;
            --accent-hover: #01ae47;
            --shadow-light: rgba(255, 255, 255, 0.05);
            --border-color: #35363a;
        }

        .container {
            max-width: 900px;
            width: 100%;
            background: var(--card-bg);
            border-radius: 18px;
            box-shadow: 0 10px 30px var(--shadow-light);
            padding: 40px;
            transition: all 0.4s ease;
        }

        /* --- Título e Cabeçalho --- */
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        
        h1 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--accent);
            margin: 0;
        }
        
        /* Base do Badge */
        .status-badge {
            font-size: 1rem;
            font-weight: 600;
            padding: 5px 12px;
            border-radius: 20px;
            transition: all 0.3s ease;
        }
        
        /* Cores de Status Dinâmicas */
        .status-badge.aguardando {
            background-color: #ffe6e6; /* Vermelho Light */
            color: var(--accent);
            border: 1px solid rgba(223, 30, 38, 0.3);
        }
        body.dark .status-badge.aguardando {
            background-color: rgba(255, 0, 0, 0.2); 
            color: var(--accent);
        }

        .status-badge.aprovado {
            background-color: #e6ffe6; /* Verde Light */
            color: var(--success);
            border: 1px solid rgba(40, 167, 69, 0.3);
        }
        body.dark .status-badge.aprovado {
            background-color: rgba(40, 167, 69, 0.3); 
            color: var(--success);
        }

        .status-badge.rejeitado {
            background-color: #f7e6ff; /* Roxo/Magenta Light */
            color: #8c42f5; 
            border: 1px solid rgba(140, 66, 245, 0.3);
        }
        body.dark .status-badge.rejeitado {
            background-color: rgba(140, 66, 245, 0.3); 
            color: #a788e0;
        }

        /* Sub-cabeçalho do Conteúdo */
        h2 {
            font-size: 1.3rem;
            font-weight: 600;
            margin-top: 30px;
            margin-bottom: 15px;
            color: var(--text-color-primary);
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 5px;
        }

        /* Link Voltar */
        .back-link { 
            color: var(--text-color-primary);
            text-decoration: none; 
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 25px;
            transition: color 0.3s ease;
        }
        .back-link:hover {
            color: var(--accent);
        }

        /* --- Conteúdo do Artigo --- */
        .article-content { 
            white-space: pre-wrap; 
            line-height: 1.75;
            font-size: 1rem;
            background-color: var(--bg-color); 
            border: 1px solid var(--border-color);
            padding: 25px;
            border-radius: 10px;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);
            color: var(--text-color-primary);
        }
        
        /* --- Seção de Ações --- */
        .actions { 
            margin-top: 40px; 
            border-top: 1px solid var(--border-color); 
            padding-top: 25px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
        }

        .action-forms {
            display: flex;
            gap: 15px;
        }
        
        /* Botões de Ação Unificado */
        .btn { 
            text-decoration: none; 
            padding: 12px 20px; 
            border-radius: 8px; 
            color: white; 
            font-weight: 700; 
            border: none; 
            cursor: pointer; 
            font-size: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        /* Cores dos Botões */
        .btn-success { 
            background-color: var(--success); 
        }
        .btn-success:hover { 
            background-color: #1e7e34; 
        }

        .btn-danger { 
            background-color: var(--danger); 
        }
        .btn-danger:hover { 
            background-color: #b72b39; 
        }
        
        /* Ajuste do botão excluir para alinhar */
        .delete-form {
            margin-left: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="{{ route('curadoria.index') }}" class="back-link"><i class="fas fa-arrow-left"></i> Voltar para a Lista</a>
        
        <div class="header-section">
            <h1>Revisão: {{ $analysis->ticker }}</h1>
            <span class="status-badge {{ strtolower($analysis->status) }}">
                Status Atual: {{ $analysis->status }}
            </span>
        </div>
        
        <h2>Conteúdo Gerado pela IA</h2>
        <div class="article-content">
            {{ $analysis->content_full }}
        </div>

        <div class="actions">
            <div class="action-forms">
                
                @if ($analysis->status == 'AGUARDANDO')
                <form action="{{ route('curadoria.update_status', $analysis->id) }}" method="POST">
                    @csrf @method('PUT') 
                    <input type="hidden" name="revisor_name" value="Lucas (Curador)">
                    
                    <button type="submit" name="status" value="APROVADO" class="btn btn-success"><i class="fas fa-check"></i> Aprovar</button>
                    <button type="submit" name="status" value="REJEITADO" class="btn btn-danger"><i class="fas fa-times"></i> Rejeitar</button>
                </form>
                @else
                @endif
            </div>

            <form action="{{ route('curadoria.destroy', $analysis->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir permanentemente esta análise?');" class="delete-form">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger"><i class="fas fa-trash-alt"></i> Excluir</button>
            </form>
        </div>
    </div>

    <script>
        const body = document.body;
        const isSystemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const savedTheme = localStorage.getItem('theme');

        if (savedTheme === 'dark' || (savedTheme === null && isSystemDark)) {
            body.classList.add('dark');
        }
        
        /*
        function toggleTheme() {
            body.classList.toggle('dark');
            localStorage.setItem('theme', body.classList.contains('dark') ? 'dark' : 'light');
        }
        */
    </script>
</body>
</html>
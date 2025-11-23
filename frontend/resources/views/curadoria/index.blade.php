<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Curadoria</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* ====== VARIÁVEIS DE TEMA FORNECIDAS E ESTILOS GERAIS ====== */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', "Inter", system-ui, -apple-system, sans-serif;
        }

        /* Paleta Light Mode */
        body {
            --bg-color: #f0f2f5; 
            --text-color-primary: #2d2d2e; 
            --text-color-accent: #df1e26; 
            --card-bg: #ffffff; 
            --accent: #df1e26;
            --accent-hover: #feb408;
            --table-header: #eef2f6; 
            --table-row-hover: #f0f4f8;
            --shadow-light: rgba(0, 0, 0, 0.08);

            background-color: var(--bg-color);
            color: var(--text-color-primary); 
            min-height: 100vh;
            padding: 60px 20px 100px;
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            display: flex;
            justify-content: center;
        }

        /* Paleta Dark Mode */
        body.dark {
            --bg-color: #1a1b1e;
            --text-color-primary: #dbd9d9; 
            --text-color-accent: #2d54a4; 
            --card-bg: #292a2d;
            --accent: #0d6efd;
            --accent-hover: #01ae47;
            --table-header: #35363a;
            --table-row-hover: #3a3a3f;
            --shadow-light: rgba(255, 255, 255, 0.05);
        }

        .container {
            width: 100%;
            max-width: 1100px;
            background-color: var(--card-bg);
            border-radius: 18px;
            box-shadow: 0 10px 30px var(--shadow-light);
            overflow: hidden;
            padding: 30px 40px 50px;
            transition: background-color 0.4s ease;
            border: 1px solid var(--table-header);
        }

        /* Título Principal */
        h1 {
            text-align: center;
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--accent); 
            margin-bottom: 30px;
            letter-spacing: -0.5px;
            text-transform: uppercase;
        }

        .top-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--table-header);
        }

        /* --- Botões de Ação --- */
        .btn {
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 12px 20px;
            border-radius: 10px;
            font-weight: 700;
            color: #fff;
            background-color: var(--accent);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .btn:hover {
            background-color: var(--accent-hover);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }
        
        .btn-revisar {
            padding: 8px 15px;
            font-weight: 600;
            font-size: 0.9rem;
            border-radius: 6px;
        }

        /* --- Switch de Tema --- */
        .theme-switch {
            --switch-w: 60px;
            --switch-h: 30px;
            --circle-size: 24px;
            width: var(--switch-w);
            height: var(--switch-h);
            background: #d6d6d6; 
            border-radius: 999px;
            position: relative;
            display: flex;
            align-items: center;
            padding: 0 3px;
            cursor: pointer;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .theme-switch .switch-circle {
            width: var(--circle-size);
            height: var(--circle-size);
            background: #fff;
            border-radius: 50%;
            position: absolute;
            top: 3px;
            left: 3px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .theme-switch i {
            font-size: 16px;
            position: absolute;
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
        
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

        body.dark .theme-switch {
            background: #3c4043; 
        }
        
        body.dark .theme-switch .switch-circle {
            left: calc(100% - var(--circle-size) - 3px);
            background: #0d6efd; 
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

        /* --- Tabela --- */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 18px rgba(0,0,0,0.08);
            border: 1px solid var(--table-header);
        }

        th, td {
            padding: 18px 15px;
            text-align: left;
            border-bottom: 1px solid var(--table-header);
            color: var(--text-color-primary); 
        }

        th {
            background-color: var(--table-header);
            color: var(--text-color-primary);
            font-weight: 700;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tr {
            transition: background-color 0.3s ease;
        }
        
        tr:last-child td {
            border-bottom: none;
        }

        tr:hover {
            background-color: var(--table-row-hover);
        }
        
        /* Cores de Status - Badge (Etiqueta) */
        .status-aguardando {
            color: #e6b200; 
            background-color: #fffbe6;
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: 700;
            display: inline-block;
            font-size: 0.85rem;
            border: 1px solid #ffcc004d;
        }
        
        body.dark .status-aguardando {
            color: #ffd700;
            background-color: #4d3e00;
            border-color: #665000;
        }

        /* Alerta de Sucesso */
        .alert {
            padding: 15px 20px;
            background-color: #e6ffed;
            color: #1a602d;
            border: 1px solid #c8e6c9;
            border-radius: 10px;
            margin-bottom: 25px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        body.dark .alert {
            background-color: #1e3c28;
            color: #a3e6b7;
            border-color: #387648;
        }
        
        .no-data-row td {
            color: #777;
            font-style: italic;
            background-color: var(--table-header);
            border-bottom: none !important;
        }
        
        body.dark .no-data-row td {
            color: #bbb;
        }

        .status-badge.status-aguardando {
            color: #e6b200; 
            background-color: #fffbe6;
            border: 1px solid #ffcc004d;
        }

        .status-badge.status-aprovado {
            color: var(--success, #28a745); 
            background-color: #e6ffed;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }

        .status-badge.status-rejeitado {
            color: var(--danger, #dc3545); 
            background-color: #ffeded;
            border: 1px solid rgba(220, 53, 69, 0.3);
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: 700;
            display: inline-block;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="top-actions">
        <a href="{{ route('index') }}" class="btn"><i class="fas fa-arrow-left"></i> Voltar</a>
        
        <div class="theme-switch" id="themeToggle">
            <i class="fas fa-sun"></i>
            <i class="fas fa-moon"></i>
            <div class="switch-circle"></div>
        </div>
    </div>

    <h1>Painel de Curadoria</h1>

    @if(session('success'))
        <div class="alert"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Ticker</th>
                <th>Status</th>
                <th>Criado em</th>
                <th>Ação</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($analyses as $analysis)
                <tr>
                    <td>**{{ $analysis->id }}**</td>
                    <td>**{{ $analysis->ticker }}**</td>
                    <td><span class="status-badge status-{{ strtolower($analysis->status) }}">{{ $analysis->status }}</span></td>
                    <td>{{ $analysis->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <a href="{{ route('curadoria.show', $analysis->id) }}" class="btn btn-revisar"><i class="fas fa-edit"></i> Revisar</a>
                    </td>
                </tr>
            @empty
                <tr class="no-data-row">
                    <td colspan="5" style="text-align: center; padding: 20px;">Nenhuma análise aguardando revisão.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
    const body = document.body;
    const themeToggle = document.getElementById('themeToggle');

    // Função para aplicar o tema
    const applyTheme = (theme) => {
        if (theme === 'dark') {
            body.classList.add('dark');
        } else {
            body.classList.remove('dark');
        }
    };

    // Carregar tema salvo ou preferência do sistema
    const isSystemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const savedTheme = localStorage.getItem('theme');

    if (savedTheme) {
        applyTheme(savedTheme);
    } else if (isSystemDark) {
        applyTheme('dark');
    } else {
        applyTheme('light');
    }

    // Toggle de tema ao clicar
    themeToggle.addEventListener('click', () => {
        const newTheme = body.classList.contains('dark') ? 'light' : 'dark';
        applyTheme(newTheme);
        localStorage.setItem('theme', newTheme);
    });
</script>
</body>
</html>
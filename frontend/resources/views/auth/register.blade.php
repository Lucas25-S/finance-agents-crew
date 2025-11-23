<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Análise de Ações com IA</title>
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
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
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
            background-color: var(--card-bg);
            padding: 40px;
            width: 100%;
            max-width: 400px;
            text-align: center;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: background-color 0.4s ease;
        }

        body.dark .app-container {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .header h1 {
            font-size: 1.8em;
            font-weight: 700;
            color: var(--accent);
            margin-top: 15px;
        }
        
        .header p {
            color: var(--subtext-color);
            opacity: 0.8;
            font-size: 0.95rem;
            margin-top: 8px;
        }

        .header .logo {
            display: block;
            margin: 0 auto 20px;
            max-width: 120px;
            height: auto;
        }

        /* ====== FORM STYLES ====== */
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-input);
            font-weight: 600;
            font-size: 0.95rem;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            background-color: var(--input-bg);
            border-radius: 8px;
            font-size: 1rem;
            color: var(--text-input);
            transition: all 0.3s ease;
        }

        body.dark .form-group input {
            border-color: #35363a;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 2px rgba(223, 30, 38, 0.1);
        }

        body.dark .form-group input:focus {
            box-shadow: 0 0 0 2px rgba(13, 110, 253, 0.1);
        }

        .form-group input::placeholder {
            color: var(--text-input);
            opacity: 0.5;
        }

        /* ====== ERROR MESSAGES ====== */
        .error-message {
            color: #dc3545;
            font-size: 0.85rem;
            margin-top: 5px;
            display: block;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.95rem;
        }

        body.dark .success-message {
            background-color: rgba(25, 135, 84, 0.2);
            color: #90EE90;
        }

        /* ====== BUTTON STYLES ====== */
        .btn {
            width: 100%;
            padding: 12px 20px;
            background-color: var(--accent);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .btn:hover {
            background-color: var(--button-hover);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* ====== LINK STYLES ====== */
        .auth-link {
            margin-top: 20px;
            text-align: center;
        }

        .auth-link p {
            color: var(--subtext-color);
            font-size: 0.95rem;
        }

        .auth-link a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .auth-link a:hover {
            text-decoration: underline;
            color: var(--button-hover);
        }

        /* ====== NOVO ESTILO TOGGLE SWITCH ====== */
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
            
            display: flex;
            align-items: center;
            padding: 0 3px;
        }

        .theme-switch .switch-circle {
            position: absolute;
            top: 3px;
            left: 3px;
            width: var(--circle-size);
            height: var(--circle-size);
            background: white;
            border-radius: 50%;
            z-index: 2;
            transition: left 0.35s cubic-bezier(.4,0,.2,1), background 0.25s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        
        .theme-switch i {
            font-size: 16px;
            position: absolute;
            z-index: 3;
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
            <img src="{{ asset('img/logo_jc.png') }}" alt="Logo JC" class="logo" />
            <h1>Criar Conta</h1>
            <p>Registre-se e comece agora</p>
        </div>

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                <span class="error-message">❌ {{ $error }}</span>
            @endforeach
        @endif

        @if (session('success'))
            <div class="success-message">
                ✅ {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('auth.register') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="name">Nome Completo</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    placeholder="Seu nome"
                    value="{{ old('name') }}"
                    required
                >
                @error('name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    placeholder="seu@email.com"
                    value="{{ old('email') }}"
                    required
                >
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Senha</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="••••••••"
                    required
                >
                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirmar Senha</label>
                <input 
                    type="password" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    placeholder="••••••••"
                    required
                >
            </div>

            <button type="submit" class="btn">
                <i class="fas fa-user-plus"></i> Criar Conta
            </button>
        </form>

        <div class="auth-link">
            <p>Já tem conta? <a href="{{ route('auth.showLogin') }}">Fazer login</a></p>
        </div>
    </div>

    <script>
    const body = document.body;
    const themeToggle = document.getElementById('themeToggle');

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
    </script>
</body>
</html>

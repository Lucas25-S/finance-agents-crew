# 🔐 Sistema de Login e Registro

## ✨ O que foi criado

Criei um sistema completo de autenticação com login e registro, mantendo o estilo CSS atual (Light/Dark Mode).

### Arquivos Criados:

1. **Migration**: `database/migrations/2025_11_20_000000_create_auth_users_table.php`
   - Cria a tabela `auth_users` com campos: id, name, email, password, email_verified_at, remember_token, timestamps

2. **Model**: `app/Models/AuthUser.php`
   - Model para interagir com a tabela auth_users

3. **Controller**: `app/Http/Controllers/AuthController.php`
   - showLogin() - Exibe página de login
   - showRegister() - Exibe página de registro
   - login() - Processa login
   - register() - Processa registro
   - logout() - Faz logout

4. **Views**:
   - `resources/views/auth/login.blade.php` - Página de login
   - `resources/views/auth/register.blade.php` - Página de registro

5. **Rotas** (adicionadas em `routes/web.php`):
   - GET /login → Mostra página de login
   - POST /login → Processa login
   - GET /register → Mostra página de registro
   - POST /register → Processa registro
   - POST /logout → Faz logout

---

## 🚀 Como Usar

### 1. Executar as Migrations

```bash
php artisan migrate
```

Isso criará a tabela `auth_users` no banco de dados.

### 2. Acessar as Páginas

- **Login**: `http://localhost/login`
- **Registro**: `http://localhost/register`

### 3. Testar

- Vá para `/register` e crie uma conta
- Vá para `/login` e faça login
- O usuário será armazenado na sessão após login bem-sucedido

---

## 🎨 Características

✅ **Mesmo CSS** da página principal (Light/Dark Mode)
✅ **Validação** de formulários (email único, senha confirmada, etc)
✅ **Segurança** com hash de senha
✅ **Sessão** para manter usuário logado
✅ **Mensagens de erro e sucesso** formatadas
✅ **Responsivo** e moderno

---

## 🔧 Próximos Passos (Opcional)

Se quiser adicionar proteção às rotas, use middleware:

```php
Route::get('/', ...)->middleware('auth.user');
```

Ou logout direto na view:

```html
@if (session('auth_user'))
    <form action="{{ route('auth.logout') }}" method="POST" style="display:inline;">
        @csrf
        <button type="submit">Logout</button>
    </form>
@endif
```

---

Tudo pronto! 🎉

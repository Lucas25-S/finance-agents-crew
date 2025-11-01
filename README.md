# 🤖 Análise Financeira com Agentes de IA (CrewAI + Gemini/Groq + Docker)

Este projeto implementa um sistema completo de análise de ações utilizando uma arquitetura de microsserviços com Docker. O frontend (Laravel) consome um backend (FastAPI/Python) que orquestra um time de agentes de IA (CrewAI) para gerar relatórios financeiros detalhados.

O fluxo cumpre todos os requisitos do desafio:
1.  **Geração por IA:** O usuário insere um ticker (ex: `PETR4.SA`).
2.  **Agentes de IA:** Um time (Júlia, Pedro, Key) coleta dados, analisa o sentimento do mercado e redige um relatório completo.
3.  **Persistência:** O relatório é salvo em um banco de dados MySQL com o status `AGUARDANDO`.
4.  **Curadoria Humana:** Uma rota `/curadoria` (CRUD) permite que o "Fator Humano" revise, aprove ou delete as análises geradas.

---

## 🚀 Arquitetura e Tecnologias

O sistema é dividido em três contêineres Docker independentes que se comunicam através de uma rede interna.

| Serviço | Tecnologia | Porta (Externa) | Função |
| :--- | :--- | :--- | :--- |
| **`frontend-web`** | **Laravel (PHP)** | `8080` | Serve a interface do usuário (Blade/JS), gerencia o CRUD e salva no banco. |
| **`backend-api`** | **FastAPI (Python)** | `8000` | Executa o CrewAI, processa os agentes de IA (Gemini/Groq) e chama APIs externas (Serper). |
| **`mysql`** | **MySQL 8.0** | `3307` | Armazena os relatórios gerados na tabela `analyses` para curadoria. |

### O Time de Agentes (CrewAI)

O cérebro do backend é um time de agentes configurado para executar tarefas em sequência:

1.  **Agente Júlia (Data Scientist):**
    * **Ferramenta:** `SerperDevTool`
    * **Objetivo:** Coletar dados financeiros estruturados (cotação, receita, lucro) da ação solicitada.
2.  **Agente Pedro (Analista de Mercado):**
    * **Ferramenta:** `SerperDevTool`
    * **Objetivo:** Analisar o sentimento do mercado e as notícias recentes sobre a empresa.
3.  **Agente Key (Jornalista/Redator):**
    * **Ferramenta:** Nenhuma (Apenas LLM)
    * **Objetivo:** Sintetizar os dados de Júlia e Pedro em um relatório final coeso, com uma recomendação clara de **COMPRAR, VENDER ou MANTER**.

---

## ⚙️ Instalação e Configuração (Obrigatório)

Siga estes passos para recriar o ambiente e rodar o projeto.

### 1. Pré-requisitos

* Docker Desktop (Docker e Docker Compose) instalado e rodando.
* Chaves de API válidas para seu LLM (ex: **Google Gemini** ou **Groq**).
* Chave de API válida do **SerperDev** (para pesquisa na Web).

### 2. Configurar Chaves de API (Raiz)

Crie um arquivo chamado **`.env`** na pasta raiz do projeto (`finance-agents/`).

```env
# .env

# --- CHAVES DO BACKEND (PYTHON/IA) ---
# Escolha UM provedor de LLM (Google ou Groq)

# Para Google Gemini:
GOOGLE_API_KEY=SUA_CHAVE_GEMINI_AQUI
GEMINI_API_KEY=SUA_CHAVE_GEMINI_AQUI # (O LiteLLM prefere este nome)

# Para Groq:
# GROQ_API_KEY=SUA_CHAVE_GROQ_AQUI

# Chave do SerperDev (para busca na web dos agentes)
SERPER_API_KEY=SUA_CHAVE_SERPER_AQUI

# --- CHAVES DO BANCO DE DADOS (MYSQL) ---
MYSQL_ROOT_PASSWORD=sua_senha_root_segura
MYSQL_DATABASE=finance_db
MYSQL_USER=laravel_user
MYSQL_PASSWORD=sua_senha_app_segura
3. Configurar Conexão do Laravel (Frontend)
Este é o passo mais crítico. O Laravel precisa saber como encontrar o contêiner do MySQL.

Abra o arquivo frontend/.env e substitua as variáveis DB_ pelas seguintes (elas devem corresponder ao que você definiu no .env raiz):

Snippet de código

# frontend/.env

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=finance_db
DB_USERNAME=laravel_user
DB_PASSWORD=sua_senha_app_segura
4. Subir os Contêineres
Execute o comando de build completo na pasta raiz. Isso irá construir as imagens, instalar as dependências (requirements.txt) e iniciar os três serviços.


docker-compose up -d --build --force-recreate
5. Preparar o Banco de Dados (Migrações)
Após os contêineres estarem no ar, você deve criar as tabelas no banco de dados.

Primeiro, crie a tabela de sessões (para evitar o erro 500):


docker-compose exec frontend-web php artisan session:table
Segundo, crie as tabelas do projeto (incluindo analyses):


docker-compose exec frontend-web php artisan migrate:fresh
🚀 Como Usar e Testar
Teste 1: Fluxo de IA (CREATE)
Acesse o Frontend: Abra o navegador em http://localhost:8080.

Execute a Análise: Digite um ticker (ex: PETR4.SA) e clique em "Analisar".

Resultado: O artigo completo aparecerá na tela, formatado por Agente. No backend, o StockController salvou este artigo no MySQL com status AGUARDANDO.

Teste 2: Curadoria Humana (READ / UPDATE)
Acesse a Curadoria: Navegue para http://localhost:8080/curadoria.

Liste (READ): Você verá a análise "AGUARDANDO" que acabou de criar.

Revise (UPDATE): Clique em "Revisar" e, na próxima tela, clique em "Aprovar Artigo". O status no banco será atualizado para APROVADO.
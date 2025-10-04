# main.py

from fastapi import FastAPI, Request
import uvicorn
import os
from crewai import Agent, Task, Crew, Process
from crewai_tools import SerperDevTool
from langchain_community.tools.yahoo_finance_news import YahooFinanceNewsTool 
from langchain_google_genai import ChatGoogleGenerativeAI

app = FastAPI()

@app.get("/")
def read_root():
    return {"message": "API de Agentes de IA está funcionando!"}

@app.post("/analyze-stock")
async def analyze_stock(request: Request):
    data = await request.json()
    stock_symbol = data.get('stock_symbol')

    if not stock_symbol:
        return {"error": "O campo 'stock_symbol' é obrigatório."}, 400

    try:
        # --- MOVEMOS TODA A LÓGICA DE IA PARA CÁ ---
        google_api_key = os.getenv("GOOGLE_API_KEY")

        # Verifica se a chave de API está faltando ou vazia
        if not google_api_key:
            return {"error": "Chave GOOGLE_API_KEY não encontrada. Verifique o arquivo chaves.env."}, 500

        # Configuração do LLM (só acontece quando a função é chamada)
        llm_google = ChatGoogleGenerativeAI(
            model="gemini-1.5-pro",
            temperature=0.7,
            google_api_key=google_api_key 
        )
        # Ferramentas que os agentes podem usar
        # Para a SerperDevTool, você precisa de uma chave de API da Serper
        search_tool = SerperDevTool()
        yahoo_finance_tool = YahooFinanceNewsTool()

        # Criação dos Agentes
        # Agente Júlia: Coletora de dados
        julia_agent = Agent(
            role='Data Scientist especializada em finanças',
            goal=f'Coletar dados financeiros atuais para a ação {stock_symbol}.',
            backstory='Júlia é uma analista de dados rigorosa que extrai informações financeiras precisas do Yahoo Finance.',
            tools=[yahoo_finance_tool],
            llm=llm_google
        )

        # Agente Pedro: Analista de mercado
        pedro_agent = Agent(
            role='Especialista em Análise de Mercado',
            goal=f'Analisar notícias e tendências do mercado sobre a ação {stock_symbol}.',
            backstory='Pedro é um especialista em mídia e mercado, com um senso aguçado para notícias que impactam preços de ações.',
            tools=[search_tool],
            llm=llm_google
        )

        # Agente Key: Jornalista e redator
        key_agent = Agent(
            role='Jornalista Financeira Sênior',
            goal=f'Redigir um artigo claro e confiável sobre a recomendação de compra/venda da ação {stock_symbol}.',
            backstory='Key é um jornalista experiente, capaz de transformar dados brutos em histórias envolventes e recomendações claras para investidores leigos.',
            llm=llm_google
        )

        # Definição das Tarefas
        task_julia = Task(
            description=f'Coletar os dados mais recentes de balanço, cotação e notícias sobre a ação {stock_symbol}.',
            agent=julia_agent,
            expected_output='Um resumo de dados financeiros atualizados e links para fontes.'
        )

        task_pedro = Task(
            description=f'Analisar a percepção do público e da mídia sobre a ação {stock_symbol} com base em notícias recentes. Use a ferramenta de busca para encontrar artigos e relatórios.',
            agent=pedro_agent,
            expected_output='Um relatório de sentimento de mercado (positivo, negativo, neutro) e os motivos por trás disso.'
        )

        task_key = Task(
            description=f'Redigir um artigo completo e fácil de entender, com base nos dados fornecidos por Júlia e Pedro. O artigo deve incluir uma recomendação clara de compra/venda/manutenção da ação {stock_symbol}.',
            agent=key_agent,
            expected_output='Um artigo de 500 palavras em formato de blog, com título, introdução, análise de dados e recomendação final.'
        )

        # Criação do Crew (o grupo de agentes)
        stock_crew = Crew(
            agents=[julia_agent, pedro_agent, key_agent],
            tasks=[task_julia, task_pedro, task_key],
            process=Process.sequential,
            verbose=True
        )

        # Início do processo e execução da análise
        final_result = stock_crew.kickoff()

        # Retorna o resultado final
        return {"message": final_result}

    except Exception as e:
        return {"message": f"ERRO NA EXECUÇÃO DA IA: {str(e)}"}, 200 

# Se você estiver rodando localmente
if __name__ == "__main__":
    uvicorn.run(app, host="0.0.0.0", port=8000)
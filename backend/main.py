# main.py - VERSÃO FINAL E FUNCIONAL (Sem Yahoo Finance Tool)

from fastapi import FastAPI, Request
import uvicorn
import os
from crewai import Agent, Task, Crew, Process
from crewai_tools import SerperDevTool # A única ferramenta de pesquisa
from langchain_community.chat_models import ChatLiteLLM
from dotenv import load_dotenv

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
        # --- LÓGICA DE INICIALIZAÇÃO ---
        
        load_dotenv()
        google_api_key = os.getenv("GOOGLE_API_KEY")

        if not google_api_key:
            return {"error": "Chave GOOGLE_API_KEY não encontrada. Verifique o arquivo chaves.env."}, 500

        # 1. Inicializa o LLM usando ChatLiteLLM (formato provedor/modelo)
        llm_google = ChatLiteLLM(
            model="gemini/gemini-2.5-flash", 
            temperature=0.7,
        )

        # 2. Inicializa a Ferramenta (SerperDevTool é a única ferramenta)
        search_tool = SerperDevTool() 

        # --- CRIAÇÃO DOS AGENTES ---
        
        # Agente Júlia: Coletora de dados (usará SerperDevTool)
        julia_agent = Agent(
            role='Data Scientist e Pesquisadora Financeira',
            goal=f'Coletar a cotação atual e os principais dados de balanço (receita, lucro, dívida) para a ação {stock_symbol} via pesquisa web.',
            backstory='Júlia é uma analista de dados rigorosa que extrai informações financeiras precisas.',
            tools=[search_tool], # CORREÇÃO: Usa apenas a ferramenta funcional
            llm=llm_google 
        )

        # Agente Pedro: Analista de mercado (usará SerperDevTool)
        pedro_agent = Agent(
            role='Especialista em Análise de Sentimento e Tendências de Mercado',
            goal=f'Analisar notícias e tendências do mercado sobre a empresa {stock_symbol} usando pesquisa web.',
            backstory='Pedro é um especialista em mídia e mercado, com senso aguçado para notícias.',
            tools=[search_tool],
            llm=llm_google 
        )

        # Agente Key: Jornalista e redator
        key_agent = Agent(
            role='Jornalista Financeira Sênior',
            goal=f'Redigir um artigo claro e confiável sobre a recomendação de compra/venda da ação {stock_symbol}.',
            backstory='Key é um jornalista experiente que transforma dados em conteúdo legível.',
            llm=llm_google
        )

        # --- DEFINIÇÃO DAS TAREFAS ---
        
        task_julia = Task(
            # A Júlia foca em dados ESTURUTURADOS usando a busca
            description=f'Use a ferramenta de busca para encontrar a cotação atual, a data do último balanço e os dados de receita e lucro líquido mais recentes da ação {stock_symbol}.',
            agent=julia_agent,
            expected_output='Um resumo conciso de dados financeiros ATUALIZADOS: Cotação, Receita e Lucro Líquido do último trimestre.'
        )

        task_pedro = Task(
            # O Pedro foca em sentimento e notícias
            description=f'Use a ferramenta de busca Serper para analisar a percepção do público e da mídia sobre a ação {stock_symbol} com base em notícias recentes e artigos de análise.',
            agent=pedro_agent,
            expected_output='Um relatório de sentimento de mercado (positivo, negativo, neutro) e os motivos por trás disso.'
        )

        task_key = Task(
            description=f'Redigir um artigo completo e fácil de entender, com título e análise de dados de Júlia e Pedro, concluindo com uma recomendação CLARA de compra, venda ou manutenção para a ação {stock_symbol}.',
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
        # Este bloco deve capturar erros de chave de API ou de limite de uso.
        return {"message": f"ERRO NA EXECUÇÃO DA IA: {str(e)}"}, 200 

# Se você estiver rodando localmente
if __name__ == "__main__":
    uvicorn.run(app, host="0.0.0.0", port=8000)
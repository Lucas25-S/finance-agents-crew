from fastapi import FastAPI, Request
import uvicorn
import os
import time
import asyncio
from crewai import Agent, Task, Crew, Process, LLM
from crewai_tools import SerperDevTool
from dotenv import load_dotenv
import litellm
import traceback

litellm.drop_params = True
litellm.set_verbose = False

app = FastAPI()

last_request_time = 0
MIN_INTERVAL = 60

@app.get("/")
def read_root():
    return {"message": "API de Agentes de IA está funcionando!"}

@app.post("/analyze-stock")
async def analyze_stock(request: Request):
    global last_request_time
    
    time_since_last = time.time() - last_request_time
    if time_since_last < MIN_INTERVAL:
        wait_time = MIN_INTERVAL - time_since_last
        return {
            "message": f"Aguarde {wait_time:.0f} segundos antes da próxima análise.",
            "wait_seconds": int(wait_time)
        }, 429
    
    data = await request.json()
    stock_symbol = data.get('stock_symbol')

    if not stock_symbol:
        return {"error": "O campo 'stock_symbol' é obrigatório."}, 400

    try:
        load_dotenv()
        last_request_time = time.time()
        
        groq_key = os.getenv("GROQ_API_KEY")
        
        from groq import Groq
        client = Groq(api_key=groq_key)
        test = client.chat.completions.create(
            model="llama-3.1-8b-instant",
            messages=[{"role": "user", "content": "OK"}],
            max_tokens=5
        )
        
        llm = LLM(
            model="groq/llama-3.1-8b-instant",
            api_key=groq_key,
            temperature=0.7
        )

        search_tool = SerperDevTool() 

        julia_agent = Agent(
            role='Analista de Dados Financeiros',
            goal=f'Coletar cotação atual e dados fundamentais de {stock_symbol}',
            backstory='Especialista em análise quantitativa e coleta de dados de mercado.',
            tools=[search_tool],
            llm=llm,
            allow_delegation=False,
            max_iter=2,
            verbose=False
        )

        task_julia = Task(
            description=f'Busque e informe APENAS a cotação atual da ação {stock_symbol}. Seja breve e objetivo.',
            agent=julia_agent,
            expected_output='Cotação atual da ação em formato: "Cotação: R$ XX.XX"'
        )

        crew_julia = Crew(
            agents=[julia_agent],
            tasks=[task_julia],
            process=Process.sequential,
            verbose=False,
            memory=False
        )
        resultado_julia = crew_julia.kickoff()
        
        await asyncio.sleep(15)

        pedro_agent = Agent(
            role='Analista de Sentimento de Mercado',
            goal=f'Avaliar o sentimento do mercado sobre {stock_symbol}',
            backstory='Especialista em análise de notícias e percepção do mercado.',
            tools=[search_tool],
            llm=llm,
            allow_delegation=False,
            max_iter=2,
            verbose=False
        )

        task_pedro = Task(
            description=f'Busque notícias recentes sobre {stock_symbol} e diga se são positivas ou negativas',
            agent=pedro_agent,
            expected_output='Sentimento: positivo ou negativo com base nas notícias'
        )

        crew_pedro = Crew(
            agents=[pedro_agent],
            tasks=[task_pedro],
            process=Process.sequential,
            verbose=False,
            memory=False
        )
        resultado_pedro = crew_pedro.kickoff()
        
        await asyncio.sleep(15)

        key_agent = Agent(
            role='Analista e Redator Financeiro',
            goal=f'Elaborar relatório final sobre {stock_symbol} com recomendação',
            backstory='Jornalista financeiro experiente em transformar dados em insights acionáveis.',
            llm=llm,
            allow_delegation=False,
            max_iter=2,
            verbose=False
        )

        task_key = Task(
            description=f'''
            Com base nas seguintes informações:
            
            DADOS FINANCEIROS (Júlia):
            {resultado_julia}
            
            SENTIMENTO DE MERCADO (Pedro):
            {resultado_pedro}
            
            Escreva um relatório estruturado sobre {stock_symbol} contendo:
            1. Resumo dos dados
            2. Análise do sentimento
            3. Recomendação final: COMPRAR, VENDER ou MANTER
            
            Seja claro, objetivo e profissional.
            ''',
            agent=key_agent,
            expected_output='Relatório estruturado com recomendação clara'
        )

        crew_key = Crew(
            agents=[key_agent],
            tasks=[task_key],
            process=Process.sequential,
            verbose=False,
            memory=False
        )
        resultado_final = crew_key.kickoff()

        return {
            "message": str(resultado_final),
            "dados": str(resultado_julia),
            "sentimento": str(resultado_pedro),
            "status": "success"
        }

    except Exception as e:
        if "rate_limit" in str(e).lower():
            return {
                "message": "Limite atingido. Aguarde 60 segundos.",
                "wait_seconds": 60
            }, 429
        
        return {"message": f"ERRO: {str(e)}"}, 500

if __name__ == "__main__":
    uvicorn.run(app, host="0.0.0.0", port=8000)
from fastapi import FastAPI, Request
import uvicorn
import os
import time
from crewai import Agent, Task, Crew, Process, LLM
from crewai_tools import SerperDevTool
from dotenv import load_dotenv

app = FastAPI()

# ✅ Variável global para controlar o tempo entre requisições
last_request_time = 0
MIN_INTERVAL = 20  # Segundos entre requisições

@app.get("/")
def read_root():
    return {"message": "API de Agentes de IA está funcionando!"}

@app.post("/analyze-stock")
async def analyze_stock(request: Request):
    global last_request_time
    
    # ✅ Verifica se precisa esperar
    time_since_last = time.time() - last_request_time
    if time_since_last < MIN_INTERVAL:
        wait_time = MIN_INTERVAL - time_since_last
        return {
            "message": f"Aguarde {wait_time:.0f} segundos antes da próxima análise (limite da API gratuita).",
            "wait_seconds": int(wait_time)
        }, 429
    
    data = await request.json()
    stock_symbol = data.get('stock_symbol')

    if not stock_symbol:
        return {"error": "O campo 'stock_symbol' é obrigatório."}, 400

    try:
        load_dotenv()
        
        # Atualiza o timestamp
        last_request_time = time.time()
        
        llm = LLM(
            model="groq/llama-3.1-8b-instant",
            api_key=os.getenv("GROQ_API_KEY")
        )

        search_tool = SerperDevTool() 

        # ✅ Agentes com descrições CURTAS
        julia_agent = Agent(
            role='Analista',
            goal=f'Dados de {stock_symbol}',
            backstory='Analista financeira.',
            tools=[search_tool],
            llm=llm,
            allow_delegation=False
        )

        pedro_agent = Agent(
            role='Sentimento',
            goal=f'Notícias {stock_symbol}',
            backstory='Analista de mercado.',
            tools=[search_tool],
            llm=llm,
            allow_delegation=False
        )

        key_agent = Agent(
            role='Redator',
            goal=f'Relatório {stock_symbol}',
            backstory='Jornalista.',
            llm=llm,
            allow_delegation=False
        )

        # ✅ Tarefas MUITO mais curtas
        task_julia = Task(
            description=f'Cotação {stock_symbol}',
            agent=julia_agent,
            expected_output='Preço atual.'
        )

        task_pedro = Task(
            description=f'Sentimento {stock_symbol}',
            agent=pedro_agent,
            expected_output='Positivo/negativo/neutro.'
        )

        task_key = Task(
            description=f'Resumo {stock_symbol}',
            agent=key_agent,
            expected_output='Análise breve com recomendação.'
        )

        stock_crew = Crew(
            agents=[julia_agent, pedro_agent, key_agent],
            tasks=[task_julia, task_pedro, task_key],
            process=Process.sequential,
            verbose=False,
            memory=False  # ✅ Desliga memória pra economizar
        )

        final_result = stock_crew.kickoff()

        return {"message": str(final_result), "status": "success"}

    except Exception as e:
        error_msg = str(e)
        
        # ✅ Detecta rate limit e informa quanto tempo esperar
        if "rate_limit_exceeded" in error_msg.lower():
            return {
                "message": "Limite de requisições atingido. Aguarde 20 segundos.",
                "wait_seconds": 20
            }, 429
        
        return {"message": f"ERRO: {error_msg}"}, 200 

if __name__ == "__main__":
    uvicorn.run(app, host="0.0.0.0", port=8000)
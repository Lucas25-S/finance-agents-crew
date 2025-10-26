from fastapi import FastAPI, Request
import uvicorn
import os
import time
import asyncio
from crewai import Agent, Task, Crew, Process, LLM
from crewai_tools import SerperDevTool
from dotenv import load_dotenv
import litellm

litellm.drop_params = True
litellm.set_verbose = False

app = FastAPI()

last_request_time = 0
MIN_INTERVAL = 30

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
            "message": f"Aguarde {wait_time:.0f} segundos.",
            "wait_seconds": int(wait_time)
        }, 429
    
    data = await request.json()
    stock_symbol = data.get('stock_symbol')

    if not stock_symbol:
        return {"error": "O campo 'stock_symbol' é obrigatório."}, 400

    try:
        load_dotenv()
        last_request_time = time.time()
        
        os.environ["GROQ_API_KEY"] = os.getenv("GROQ_API_KEY")
        
        llm = LLM(
            model="groq/llama-3.1-8b-instant",
            api_key=os.getenv("GROQ_API_KEY"),
            temperature=0.7
        )

        search_tool = SerperDevTool() 

        # AGENTE 1 - Júlia
        julia_agent = Agent(
            role='Coletora de Dados',
            goal=f'Buscar cotação de {stock_symbol}',
            backstory='Analista de dados.',
            tools=[search_tool],
            llm=llm,
            allow_delegation=False,
            max_iter=2
        )

        task_julia = Task(
            description=f'Busque apenas a cotação atual de {stock_symbol}',
            agent=julia_agent,
            expected_output='Preço atual da ação.'
        )

        crew_julia = Crew(
            agents=[julia_agent],
            tasks=[task_julia],
            process=Process.sequential,
            verbose=False,
            memory=False
        )
        resultado_julia = crew_julia.kickoff()
        
        await asyncio.sleep(10)

        # AGENTE 2 - Pedro
        pedro_agent = Agent(
            role='Analista de Sentimento',
            goal=f'Analisar notícias sobre {stock_symbol}',
            backstory='Especialista em mercado.',
            tools=[search_tool],
            llm=llm,
            allow_delegation=False,
            max_iter=2
        )

        task_pedro = Task(
            description=f'Analise o sentimento de mercado sobre {stock_symbol}',
            agent=pedro_agent,
            expected_output='Sentimento: positivo, negativo ou neutro.'
        )

        crew_pedro = Crew(
            agents=[pedro_agent],
            tasks=[task_pedro],
            process=Process.sequential,
            verbose=False,
            memory=False
        )
        resultado_pedro = crew_pedro.kickoff()
        
        await asyncio.sleep(10)

        # AGENTE 3 - Key
        key_agent = Agent(
            role='Redator',
            goal=f'Escrever relatório sobre {stock_symbol}',
            backstory='Jornalista financeiro.',
            llm=llm,
            allow_delegation=False,
            max_iter=2
        )

        task_key = Task(
            description=f'Com base nos dados: {resultado_julia} e sentimento: {resultado_pedro}, escreva um relatório sobre {stock_symbol} com recomendação de compra/venda/manter',
            agent=key_agent,
            expected_output='Relatório com recomendação clara.'
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
        error_msg = str(e)
        
        if "rate_limit" in error_msg.lower():
            return {
                "message": "Limite atingido. Tente novamente em 30 segundos.",
                "wait_seconds": 30
            }, 429
        
        return {"message": f"ERRO: {error_msg}"}, 500

if __name__ == "__main__":
    uvicorn.run(app, host="0.0.0.0", port=8000)
from dotenv import load_dotenv
import os
from groq import Groq

load_dotenv()

groq_key = os.getenv("GROQ_API_KEY")
print(f"Chave carregada: {groq_key[:20]}...")

try:
    client = Groq(api_key=groq_key)
    
    response = client.chat.completions.create(
        model="llama-3.1-8b-instant",
        messages=[{"role": "user", "content": "Diga apenas: OK"}],
        max_tokens=10
    )
    
    print(f"✅ FUNCIONA! Resposta: {response.choices[0].message.content}")
    
except Exception as e:
    print(f"❌ ERRO: {e}")
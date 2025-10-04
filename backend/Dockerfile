# Use a imagem oficial do Python como base
FROM python:3.11-slim

# Define o diretório de trabalho dentro do container
WORKDIR /app

# Copia o arquivo de dependências e o instala primeiro para otimizar o cache do Docker
COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# Copia o restante do código da sua aplicação
COPY . .

# Expõe a porta que a API do FastAPI vai usar
EXPOSE 8000

# Comando para iniciar o servidor Uvicorn quando o container for iniciado
CMD ["uvicorn", "main:app", "--host", "0.0.0.0", "--port", "8000"]
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revisar Análise: {{ $analysis->ticker }}</title>
    <style>
        body { font-family: sans-serif; background-color: #f4f4f9; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); padding: 30px; }
        h1 { border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .article-content { white-space: pre-wrap; /* Preserva as quebras de linha da IA */ line-height: 1.6; background-color: #fdfdfd; border: 1px solid #eee; padding: 20px; border-radius: 5px; margin-top: 20px; }
        .actions { margin-top: 30px; border-top: 2px solid #eee; padding-top: 20px; display: flex; justify-content: space-between; align-items: center; }
        .btn { text-decoration: none; padding: 10px 15px; border-radius: 5px; color: white; font-weight: bold; border: none; cursor: pointer; font-size: 16px; }
        .btn-success { background-color: #28a745; }
        .btn-warning { background-color: #ffc107; color: #333; }
        .btn-danger { background-color: #dc3545; }
        .back-link { color: #007bff; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <a href="{{ route('curadoria.index') }}" class="back-link">&larr; Voltar para a Lista</a>
        
        <h1>Revisão: {{ $analysis->ticker }}</h1>
        <p>Status Atual: <strong>{{ $analysis->status }}</strong></p>

        <h2>Artigo Gerado pela IA</h2>
        <div class="article-content">
            {{ $analysis->content_full }}
        </div>

        <div class="actions">
            
            <form action="{{ route('curadoria.update_status', $analysis->id) }}" method="POST">
                @csrf @method('PUT') <input type="hidden" name="revisor_name" value="Lucas (Curador)">
                
                <button type="submit" name="status" value="APROVADO" class="btn btn-success">Aprovar Artigo</button>
                <button type="submit" name="status" value="REJEITADO" class="btn btn-warning">Rejeitar Artigo</button>
            </form>

            <form action="{{ route('curadoria.destroy', $analysis->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta análise?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Excluir</button>
            </form>
        </div>

    </div>
</body>
</html>
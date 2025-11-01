<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Curadoria de Análises</title>
    <style>
        body { font-family: sans-serif; background-color: #f4f4f9; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 900px; margin: auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); overflow: hidden; }
        h1 { text-align: center; padding: 20px 0; background-color: #4a5568; color: white; margin: 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background-color: #f9fafb; }
        tr:hover { background-color: #f1f1f1; }
        .status-aguardando { color: #f59e0b; font-weight: bold; }
        .btn { text-decoration: none; padding: 8px 12px; border-radius: 5px; color: white; font-weight: bold; }
        .btn-revisar { background-color: #007bff; }
        .btn-revisar:hover { background-color: #0056b3; }
        .alert { padding: 15px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 5px; margin: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Painel de Curadoria</h1>

        @if(session('success'))
            <div class="alert">{{ session('success') }}</div>
        @endif

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ticker</th>
                    <th>Status</th>
                    <th>Criado em</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($analyses as $analysis)
                    <tr>
                        <td>{{ $analysis->id }}</td>
                        <td>{{ $analysis->ticker }}</td>
                        <td><span class="status-aguardando">{{ $analysis->status }}</span></td>
                        <td>{{ $analysis->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <a href="{{ route('curadoria.show', $analysis->id) }}" class="btn btn-revisar">Revisar</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 20px;">Nenhuma análise aguardando revisão.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>
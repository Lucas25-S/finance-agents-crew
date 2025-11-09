<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; // Mantido para caso precise de algo no futuro
use App\Models\Analysis; // Model para o CRUD
use App\Http\Controllers\Controller; 

class CuradoriaController extends Controller
{
    // READ (Lista): Lista análises para curadoria (AGUARDANDO)
    public function index()
    {
        // O READ (R do CRUD)
        $analyses = Analysis::all();
        // Retorna para uma view que você criaria (curadoria/index.blade.php)
        return view('curadoria.index', compact('analyses')); 
    }

    // READ (Detalhe): Mostra o artigo completo
    public function show($id)
    {
        // O READ de um item (R do CRUD)
        $analysis = Analysis::findOrFail($id);
        return view('curadoria.show', compact('analysis'));
    }

    // UPDATE (Restrito): Altera apenas o status e o revisor
    public function updateStatus(Request $request, $id)
    {
        $analysis = Analysis::findOrFail($id);
        
        // 1. Validação dos dados
        $request->validate([
            'status' => 'required|in:AGUARDANDO,APROVADO,REJEITADO,PUBLICADO', // Garante que o status é válido
            'revisor_name' => 'nullable|string|max:50', // Permite que o nome do revisor seja passado
        ]);

        // 2. Aplica o UPDATE restrito (U do CRUD)
        $analysis->update([
            'status' => $request->status, 
            // Usa o nome vindo da requisição, ou usa um valor padrão se for nulo
            'revisor_name' => $request->revisor_name ?? (auth()->user()->name ?? 'Curador Manual'), 
        ]);

        return redirect()->route('curadoria.index')->with('success', 'Status da análise atualizado para ' . $request->status . '.');
    }

    // DELETE: Remove uma análise
    public function destroy($id)
    {
        // O DELETE (D do CRUD)
        Analysis::destroy($id);
        return redirect()->route('curadoria.index')->with('success', 'Análise deletada com sucesso.');
    }

    
}
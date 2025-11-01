<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller; 
use App\Models\Analysis; // <--- NOVO: Importa o Model de Análise

class StockController extends Controller
{
    /**
     * Analisa uma ação fazendo uma requisição ao backend de IA e salva o resultado no banco.
     */
    public function analyze($symbol)
    {
        try {
        $response = Http::timeout(300)->post("http://backend-api:8000/analyze-stock", [
        'stock_symbol' => $symbol,
            ]);

            $data = $response->json() ?? [];

            \Log::info('=== DEBUG BACKEND ===');
            \Log::info('Status HTTP: ' . $response->status());
            \Log::info('Response Body: ' . $response->body());
            \Log::info('Data parsed: ', $data);
            \Log::info('Failed? ' . ($response->failed() ? 'SIM' : 'NÃO'));
            \Log::info('Tem error key? ' . (isset($data['error']) ? 'SIM' : 'NÃO'));
                
            // 1. Verifica se houve erro HTTP ou erro retornado pelo backend
            if ($response->failed() || isset($data['error'])) {
                $errorMessage = $data['error'] ?? 'Erro desconhecido ao processar no backend.';
                return response()->json(['error' => $errorMessage], $response->status());
            }

            // 2. Verifica se precisa aguardar (erro 429 - rate limit)
            if ($response->status() === 429) {
                $waitSeconds = $data['wait_seconds'] ?? 60;
                return response()->json([
                    'error' => $data['message'] ?? 'Limite atingido. Aguarde antes de fazer nova requisição.',
                    'wait_seconds' => $waitSeconds
                ], 429);
            }

            // --- LÓGICA DE EXTRAÇÃO E FORMATAÇÃO ---
            
            $formattedOutput = '';
            $resultData = $data;
            
            // Variáveis de resultado do Crew (retornadas explicitamente pelo backend)
            $resultadoJulia = $data['dados'] ?? 'Dados financeiros não coletados.';
            $resultadoPedro = $data['sentimento'] ?? 'Análise de sentimento não concluída.';
            $resultadoFinal = $data['message'] ?? 'Relatório final da Key não encontrado.';
            
            // --- CONSTRÓI A SAÍDA FORMATADA PARA EXIBIÇÃO ---

            $formattedOutput .= "================================================\n";
            $formattedOutput .= "AGENTE: DATA SCIENTIST (JÚLIA)\n";
            $formattedOutput .= "================================================\n";
            $formattedOutput .= $resultadoJulia . "\n\n";
            
            $formattedOutput .= "================================================\n";
            $formattedOutput .= "AGENTE: ANALISTA DE SENTIMENTO (PEDRO)\n";
            $formattedOutput .= "================================================\n";
            $formattedOutput .= $resultadoPedro . "\n\n";
            
            $formattedOutput .= "================================================\n";
            $formattedOutput .= "AGENTE: JORNALISTA/REDATOR (KEY)\n";
            $formattedOutput .= "================================================\n";
            $formattedOutput .= $resultadoFinal . "\n\n";
            
            // --- PERSISTÊNCIA: CREATE NO BANCO DE DADOS ---
            
            // O conteúdo completo para salvar no banco é o artigo final (resultadoFinal)
            $analysis = Analysis::create([
                'ticker' => $symbol,
                'content_full' => $resultadoFinal, // O artigo final para revisão
                'content_raw' => json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), // O JSON completo de debug
                'status' => 'AGUARDANDO', // Status inicial para Curadoria Humana
                'revisor_name' => null,
            ]);

            // Retorna o JSON que o frontend espera (com a análise completa e o ID do novo registro)
            return response()->json([
                'message' => $formattedOutput,
                'status' => $data['status'] ?? 'success',
                'id' => $analysis->id, // Retorna o ID para possível link de curadoria
                'details' => $data
            ], 200);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            // Tratamento específico para Timeout ou erro de conexão
            return response()->json([
                'error' => 'Tempo de conexão esgotado ou servidor inacessível. A análise pode demorar até 60 segundos.'
            ], 504);
        } catch (\Exception $e) {
            // Outros erros
            return response()->json([
                'error' => 'Erro interno ao processar a requisição: ' . $e->getMessage()
            ], 500);
        }
    }
}
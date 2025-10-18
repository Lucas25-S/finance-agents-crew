<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller; 

class StockController extends Controller
{
    /**
     * Analisa uma ação fazendo uma requisição ao backend de IA.
     */
    public function analyze($symbol)
    {
        try {
            // CORREÇÃO DA URL E TIMEOUT: Usa o nome do serviço 'backend-api' e aumenta o timeout
            $response = Http::timeout(120)->post("http://backend-api:8000/analyze-stock", [
                'stock_symbol' => $symbol,
            ]);

            // Tenta obter o JSON retornado.
            $data = $response->json() ?? [];
            
            // 1. Verifica se a resposta foi um erro HTTP ou se o backend retornou uma chave 'error'.
            if ($response->failed() || isset($data['error'])) {
                // Tenta pegar o erro do JSON, senão usa uma mensagem genérica de falha HTTP
                $errorMessage = $data['error'] ?? 'Erro desconhecido ao processar no backend.';
                return response()->json(['error' => $errorMessage], $response->status());
            }

            // --- LÓGICA DE EXTRAÇÃO E FORMATAÇÃO ESTRUTURADA ---
            
            $formattedOutput = '';
            $resultData = $data['message'] ?? $data; // Pega o conteúdo principal
            $tasksOutput = $resultData['tasks_output'] ?? null;
            
            if ($tasksOutput) {
                // Itera sobre as tarefas para formatar a saída de cada agente
                foreach ($tasksOutput as $task) {
                    $agentName = $task['agent'] ?? 'Agente Desconhecido';
                    $agentRawOutput = $task['raw'] ?? 'Nenhum conteúdo gerado.';

                    // Adiciona o cabeçalho e o conteúdo formatado
                    $formattedOutput .= "================================================\n";
                    $formattedOutput .= "AGENTE: " . strtoupper($agentName) . "\n";
                    $formattedOutput .= "================================================\n";
                    $formattedOutput .= $agentRawOutput . "\n\n";
                }
            }

            // Adiciona o resultado final do CrewAI (o artigo final da Key)
            if (isset($resultData['raw'])) {
                 $formattedOutput .= "================================================\n";
                 $formattedOutput .= "REDAÇÃO FINAL (ARTIGO COMPLETO)\n";
                 $formattedOutput .= "================================================\n";
                 $formattedOutput .= $resultData['raw'] . "\n\n";
            } else if (!$formattedOutput) {
                // Caso o formato não seja o esperado (e não houve tasks), retorna o JSON para debug.
                $formattedOutput = json_encode($resultData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            }

            // CORREÇÃO FINAL: Retorna o JSON com a chave 'message' (com a string formatada)
            // O JavaScript espera esta chave!
            return response()->json([
                'message' => $formattedOutput,
                // Opcional: Retorna os detalhes brutos para debug no console do navegador
                'details' => $resultData
            ], 200);

        } catch (\Exception $e) {
            // Em caso de erro de rede ou timeout (cURL), retorna o erro.
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
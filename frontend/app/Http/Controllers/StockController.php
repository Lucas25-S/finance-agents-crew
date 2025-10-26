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
            // ✅ Aumentei o timeout para 60 segundos (porque agora demora ~35-45s)
            $response = Http::timeout(60)->post("http://backend-api:8000/analyze-stock", [
                'stock_symbol' => $symbol,
            ]);

            $data = $response->json() ?? [];
            
            // Verifica se houve erro HTTP ou erro retornado pelo backend
            if ($response->failed() || isset($data['error'])) {
                $errorMessage = $data['error'] ?? 'Erro desconhecido ao processar no backend.';
                return response()->json(['error' => $errorMessage], $response->status());
            }

            // ✅ Verifica se precisa aguardar (erro 429 - rate limit)
            if ($response->status() === 429) {
                $waitSeconds = $data['wait_seconds'] ?? 30;
                return response()->json([
                    'error' => $data['message'] ?? 'Aguarde antes de fazer nova requisição.',
                    'wait_seconds' => $waitSeconds
                ], 429);
            }

            // --- NOVA ESTRUTURA DE RESPOSTA ---
            
            $formattedOutput = '';
            
            // ✅ A nova API retorna 'message', 'dados' e 'sentimento'
            $mensagemFinal = $data['message'] ?? '';
            $dados = $data['dados'] ?? '';
            $sentimento = $data['sentimento'] ?? '';
            
            // Formata a saída estruturada
            if ($dados) {
                $formattedOutput .= "================================================\n";
                $formattedOutput .= "DADOS FINANCEIROS (JÚLIA)\n";
                $formattedOutput .= "================================================\n";
                $formattedOutput .= $dados . "\n\n";
            }
            
            if ($sentimento) {
                $formattedOutput .= "================================================\n";
                $formattedOutput .= "ANÁLISE DE SENTIMENTO (PEDRO)\n";
                $formattedOutput .= "================================================\n";
                $formattedOutput .= $sentimento . "\n\n";
            }
            
            if ($mensagemFinal) {
                $formattedOutput .= "================================================\n";
                $formattedOutput .= "RELATÓRIO FINAL (KEY)\n";
                $formattedOutput .= "================================================\n";
                $formattedOutput .= $mensagemFinal . "\n\n";
            }
            
            // Se não tiver estrutura, retorna o que vier
            if (!$formattedOutput) {
                $formattedOutput = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            }

            return response()->json([
                'message' => $formattedOutput,
                'status' => $data['status'] ?? 'success',
                'details' => $data
            ], 200);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            // Timeout ou erro de conexão
            return response()->json([
                'error' => 'Tempo de conexão esgotado. A análise pode demorar até 60 segundos.'
            ], 504);
        } catch (\Exception $e) {
            // Outros erros
            return response()->json([
                'error' => 'Erro ao conectar com o backend: ' . $e->getMessage()
            ], 500);
        }
    }
}
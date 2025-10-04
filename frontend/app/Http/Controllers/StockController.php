<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class StockController extends Controller
{
    /**
     * Analisa uma ação fazendo uma requisição ao backend de IA.
     */
    public function analyze($symbol)
    {
        try {
            // A URL para o seu backend em Python. Use o nome do serviço do Docker Compose.
            $response = Http::post("http://backend-api:8000/analyze-stock", [
                'stock_symbol' => $symbol,
            ]);

            // Retorne a resposta da API do Python como JSON.
            // Isso pode ser uma mensagem de sucesso ou o conteúdo gerado.
            return response()->json($response->json());

        } catch (\Exception $e) {
            // Em caso de erro, retorne a mensagem de erro da requisição.
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
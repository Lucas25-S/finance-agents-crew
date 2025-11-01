<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('analyses', function (Blueprint $table) {
            $table->id();
            
            // Colunas de Dados do Desafio
            $table->string('ticker', 10); // O símbolo da ação (e.g., PETR4)
            $table->longText('content_full');  // O artigo final gerado pelo Agente Key
            $table->longText('content_raw');   // O JSON bruto da execução do CrewAI (para referência)
            
            // Colunas de Curadoria (Fator Humano)
            $table->enum('status', ['AGUARDANDO', 'APROVADO', 'REJEITADO', 'PUBLICADO'])
                  ->default('AGUARDANDO'); // A coluna que será atualizada (o "U" do CRUD)
            $table->string('revisor_name')->nullable(); // Nome do usuário/pessoa que aprovou/rejeitou
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analyses');
    }
};
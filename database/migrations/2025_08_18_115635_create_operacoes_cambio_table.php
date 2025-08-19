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
        Schema::create('operacoes_cambio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('conta_origem_id')->nullable()->constrained('contas')->onDelete('set null');
            $table->foreignId('conta_destino_id')->nullable()->constrained('contas')->onDelete('set null');
            $table->foreignId('moeda_origem_id')->constrained('moedas')->onDelete('restrict');
            $table->foreignId('moeda_destino_id')->constrained('moedas')->onDelete('restrict');
            $table->decimal('valor_origem', 20, 2);
            $table->decimal('valor_destino', 20, 2);
            $table->decimal('taxa_utilizada', 20, 8);
            $table->timestamp('data_operacao')->useCurrent();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('cliente_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operacoes_cambio');
    }
};

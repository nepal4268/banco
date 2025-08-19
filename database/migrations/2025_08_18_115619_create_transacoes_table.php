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
        Schema::create('transacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conta_origem_id')->nullable()->constrained('contas')->onDelete('set null');
            $table->boolean('origem_externa')->default(false);
            $table->string('conta_externa_origem', 64)->nullable();
            $table->string('banco_externo_origem', 100)->nullable();

            $table->foreignId('conta_destino_id')->nullable()->constrained('contas')->onDelete('set null');
            $table->boolean('destino_externa')->default(false);
            $table->string('conta_externa_destino', 64)->nullable();
            $table->string('banco_externo_destino', 100)->nullable();

            $table->foreignId('tipo_transacao_id')->constrained('tipos_transacao')->onDelete('restrict');
            $table->decimal('valor', 20, 2);
            $table->foreignId('moeda_id')->constrained('moedas')->onDelete('restrict');
            $table->foreignId('status_transacao_id')->constrained('status_transacao')->onDelete('restrict');
            $table->string('descricao', 255)->nullable();
            $table->string('referencia_externa', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('conta_origem_id');
            $table->index('conta_destino_id');
            $table->index('origem_externa');
            $table->index('destino_externa');
            
            // Note: Check constraint (conta_origem_id <> conta_destino_id) will be handled at application level
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transacoes');
    }
};

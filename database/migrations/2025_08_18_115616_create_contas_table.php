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
        Schema::create('contas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('agencia_id')->constrained('agencias')->onDelete('restrict');
            $table->string('numero_conta', 20)->unique();
            $table->foreignId('tipo_conta_id')->constrained('tipos_conta')->onDelete('restrict');
            $table->foreignId('moeda_id')->constrained('moedas')->onDelete('restrict');
            $table->decimal('saldo', 20, 2)->default(0.00);
            $table->string('iban', 34)->unique()->nullable();
            $table->foreignId('status_conta_id')->constrained('status_conta')->onDelete('restrict');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('cliente_id');
            $table->index('agencia_id');
            $table->index('numero_conta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contas');
    }
};

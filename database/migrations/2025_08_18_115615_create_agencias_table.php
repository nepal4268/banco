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
        Schema::create('agencias', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_banco', 4)->default('0042');
            $table->string('codigo_agencia', 4)->unique();
            $table->string('nome', 100);
            $table->string('endereco', 255);
            $table->json('telefone')->nullable(); // Array de telefones no formato JSON
            $table->string('cidade', 100)->nullable();
            $table->string('provincia', 100)->nullable();
            $table->string('gerente', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->boolean('ativa')->default(true);
            
            $table->index(['codigo_banco', 'codigo_agencia']);
            $table->index('ativa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agencias');
    }
};
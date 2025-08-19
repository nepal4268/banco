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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100);
            $table->enum('sexo', ['M', 'F']);
            $table->string('bi', 25)->unique();
            $table->string('email', 100)->unique()->nullable();
            $table->json('telefone')->nullable();
            $table->date('data_nascimento')->nullable();
            $table->string('endereco', 255)->nullable();
            $table->string('cidade', 100)->nullable();
            $table->string('provincia', 100)->nullable();
            $table->foreignId('tipo_cliente_id')->constrained('tipos_cliente')->onDelete('restrict');
            $table->foreignId('status_cliente_id')->constrained('status_cliente')->onDelete('restrict');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('bi');
            $table->index('nome');
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};

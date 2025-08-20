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
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100);
            $table->string('email', 100)->unique();
            $table->string('bi', 25)->unique();
            $table->enum('sexo', ['M', 'F']);
            $table->date('data_nascimento')->nullable();
            $table->json('telefone')->nullable();
            $table->string('senha', 255);
            $table->foreignId('perfil_id')->nullable()->constrained('perfis')->onDelete('set null');
            $table->foreignId('agencia_id')->nullable();
            $table->string('status_usuario', 30)->default('ativo');
            $table->string('endereco', 255)->nullable();
            $table->string('cidade', 100)->nullable();
            $table->string('provincia', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('email');
            $table->index('perfil_id');
            $table->index('agencia_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};

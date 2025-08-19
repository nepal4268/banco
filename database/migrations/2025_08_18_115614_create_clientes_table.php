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
            $table->foreignId('tipo_cliente_id')->constrained('tipos_cliente')->onDelete('restrict');
            $table->foreignId('status_cliente_id')->constrained('status_cliente')->onDelete('restrict');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('bi');
            $table->index('nome');
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
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
        Schema::create('apolices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('tipo_seguro_id')->constrained('tipos_seguro')->onDelete('restrict');
            $table->string('numero_apolice', 50)->unique();
            $table->date('inicio_vigencia');
            $table->date('fim_vigencia');
            $table->foreignId('status_apolice_id')->constrained('status_apolice')->onDelete('restrict');
            $table->decimal('premio_mensal', 20, 2);
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
        Schema::dropIfExists('apolices');
    }
};

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
        Schema::create('taxas_cambio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('moeda_origem_id')->constrained('moedas')->onDelete('restrict');
            $table->foreignId('moeda_destino_id')->constrained('moedas')->onDelete('restrict');
            $table->decimal('taxa', 20, 8);
            $table->date('data_taxa');
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['moeda_origem_id', 'moeda_destino_id', 'data_taxa'], 'uk_taxa_moedas_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxas_cambio');
    }
};

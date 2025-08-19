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
        Schema::create('sinistros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apolice_id')->constrained('apolices')->onDelete('cascade');
            $table->text('descricao')->nullable();
            $table->decimal('valor_reivindicado', 20, 2);
            $table->decimal('valor_pago', 20, 2)->default(0);
            $table->date('data_sinistro');
            $table->foreignId('status_sinistro_id')->constrained('status_sinistro')->onDelete('restrict');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('apolice_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sinistros');
    }
};

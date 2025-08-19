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
        Schema::create('cartoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conta_id')->constrained('contas')->onDelete('cascade');
            $table->foreignId('tipo_cartao_id')->constrained('tipos_cartao')->onDelete('restrict');
            // Armazenamos o número do cartão criptografado e garantimos unicidade via hash determinístico
            $table->text('numero_cartao');
            $table->string('numero_cartao_hash', 64)->unique();
            $table->date('validade');
            $table->foreignId('status_cartao_id')->constrained('status_cartao')->onDelete('restrict');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('conta_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cartoes');
    }
};

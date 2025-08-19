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
        Schema::create('pagamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conta_id')->constrained('contas')->onDelete('cascade');
            $table->string('parceiro', 100);
            $table->string('referencia', 100);
            $table->decimal('valor', 20, 2);
            $table->foreignId('moeda_id')->constrained('moedas')->onDelete('restrict');
            $table->timestamp('data_pagamento')->useCurrent();
            $table->foreignId('status_pagamento_id')->constrained('status_pagamento')->onDelete('restrict');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('conta_id');
            $table->index('referencia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagamentos');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Esta migration tornou-se redundante pois `agencia_id` foi movido para a criação de `contas`.
// Mantemos uma migration no-op para compatibilidade com ordem de execução.
return new class extends Migration
{
    public function up(): void
    {
        // No operation
    }

    public function down(): void
    {
        // No operation
    }
};
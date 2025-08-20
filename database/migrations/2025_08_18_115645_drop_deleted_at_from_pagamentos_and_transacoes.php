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
        if (Schema::hasColumn('transacoes', 'deleted_at')) {
            Schema::table('transacoes', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasColumn('pagamentos', 'deleted_at')) {
            Schema::table('pagamentos', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transacoes', function (Blueprint $table) {
            if (!Schema::hasColumn('transacoes', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('pagamentos', function (Blueprint $table) {
            if (!Schema::hasColumn('pagamentos', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }
};



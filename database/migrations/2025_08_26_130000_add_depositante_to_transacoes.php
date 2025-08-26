<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transacoes', function (Blueprint $table) {
            if (!Schema::hasColumn('transacoes', 'depositante')) {
                $table->string('depositante', 150)->nullable()->after('descricao');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transacoes', function (Blueprint $table) {
            if (Schema::hasColumn('transacoes', 'depositante')) {
                $table->dropColumn('depositante');
            }
        });
    }
};

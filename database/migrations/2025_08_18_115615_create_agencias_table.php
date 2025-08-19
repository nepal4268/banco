<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('agencias', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_banco', 4)->default('0042');
            $table->string('codigo_agencia', 4)->unique();
            $table->string('nome', 100);
            $table->string('endereco', 255);
            $table->json('telefones')->nullable(); // Array de telefones no formato JSON
            $table->string('email', 100)->nullable();
            $table->boolean('ativa')->default(true);
            
            $table->index(['codigo_banco', 'codigo_agencia']);
            $table->index('ativa');
        });

        // Inserir agência padrão
        DB::table('agencias')->insert([
            [
                'codigo_banco' => '0042',
                'codigo_agencia' => '0001',
                'nome' => 'Agência Central',
                'endereco' => 'Luanda, Angola',
                'telefones' => json_encode(['930202034']),
                'email' => 'central@banco.ao',
                'ativa' => true,
            ],
            [
                'codigo_banco' => '0042',
                'codigo_agencia' => '0002',
                'nome' => 'Agência Norte',
                'endereco' => 'Luanda Norte, Angola',
                'telefones' => json_encode(['930202035', '930202036']),
                'email' => 'norte@banco.ao',
                'ativa' => true,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agencias');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('status_sinistro', function (Blueprint $table) {
			$table->id();
			$table->string('nome', 30)->unique();
			$table->string('descricao', 255)->nullable();
		});

		DB::table('status_sinistro')->insert([
			['nome' => 'Aberto', 'descricao' => 'Sinistro aberto'],
			['nome' => 'Em análise', 'descricao' => 'Sinistro em análise'],
			['nome' => 'Aprovado', 'descricao' => 'Sinistro aprovado'],
			['nome' => 'Negado', 'descricao' => 'Sinistro negado'],
			['nome' => 'Pago', 'descricao' => 'Sinistro pago'],
			['nome' => 'Encerrado', 'descricao' => 'Sinistro encerrado'],
		]);
	}

	public function down(): void
	{
		Schema::dropIfExists('status_sinistro');
	}
};
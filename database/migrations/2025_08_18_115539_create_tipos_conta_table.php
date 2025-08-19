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
		Schema::create('tipos_conta', function (Blueprint $table) {
			$table->id();
			$table->string('nome', 30)->unique();
			$table->string('descricao', 255)->nullable();
		});

		DB::table('tipos_conta')->insert([
			['nome' => 'Conta Corrente', 'descricao' => 'Conta corrente para movimentação diária'],
			['nome' => 'Conta Poupança', 'descricao' => 'Conta poupança para aplicações'],
			['nome' => 'Conta Salário', 'descricao' => 'Conta específica para recebimento de salário'],
			['nome' => 'Conta Empresarial', 'descricao' => 'Conta para pessoas jurídicas'],
			['nome' => 'Conta Premium', 'descricao' => 'Conta premium com benefícios especiais'],
		]);
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('tipos_conta');
	}
};
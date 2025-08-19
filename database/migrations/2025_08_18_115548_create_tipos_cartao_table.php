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
		Schema::create('tipos_cartao', function (Blueprint $table) {
			$table->id();
			$table->string('nome', 30)->unique();
			$table->string('descricao', 255)->nullable();
		});

		DB::table('tipos_cartao')->insert([
			['nome' => 'Débito', 'descricao' => 'Cartão de débito'],
			['nome' => 'Crédito', 'descricao' => 'Cartão de crédito'],
			['nome' => 'Pré-pago', 'descricao' => 'Cartão pré-pago recarregável'],
			['nome' => 'Virtual', 'descricao' => 'Cartão virtual para compras online'],
			['nome' => 'Empresarial', 'descricao' => 'Cartão corporativo para empresas'],
		]);
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('tipos_cartao');
	}
};
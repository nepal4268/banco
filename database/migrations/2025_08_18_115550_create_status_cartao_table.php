<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('status_cartao', function (Blueprint $table) {
			$table->id();
			$table->string('nome', 30)->unique();
			$table->string('descricao', 255)->nullable();
		});

		DB::table('status_cartao')->insert([
			['nome' => 'Ativo', 'descricao' => 'Cartão ativo'],
			['nome' => 'Bloqueado', 'descricao' => 'Cartão bloqueado'],
			['nome' => 'Expirado', 'descricao' => 'Cartão expirado'],
			['nome' => 'Cancelado', 'descricao' => 'Cartão cancelado'],
			['nome' => 'Pendente', 'descricao' => 'Cartão pendente de ativação'],
		]);
	}

	public function down(): void
	{
		Schema::dropIfExists('status_cartao');
	}
};
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
		Schema::create('status_cliente', function (Blueprint $table) {
			$table->id();
			$table->string('nome', 30)->unique();
			$table->string('descricao', 255)->nullable();
		});

		DB::table('status_cliente')->insert([
			['nome' => 'Ativo', 'descricao' => 'Cliente ativo no sistema'],
			['nome' => 'Inativo', 'descricao' => 'Cliente inativo temporariamente'],
			['nome' => 'Bloqueado', 'descricao' => 'Cliente bloqueado por violação'],
			['nome' => 'Suspenso', 'descricao' => 'Cliente suspenso administrativamente'],
			['nome' => 'Pendente', 'descricao' => 'Cliente aguardando aprovação'],
		]);
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('status_cliente');
	}
};
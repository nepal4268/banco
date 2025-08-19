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
		Schema::create('status_transacao', function (Blueprint $table) {
			$table->id();
			$table->string('nome', 30)->unique();
			$table->string('descricao', 255)->nullable();
		});

		DB::table('status_transacao')->insert([
			['nome' => 'Pendente', 'descricao' => 'Transação aguardando processamento'],
			['nome' => 'Processando', 'descricao' => 'Transação sendo processada'],
			['nome' => 'Concluída', 'descricao' => 'Transação concluída com sucesso'],
			['nome' => 'Cancelada', 'descricao' => 'Transação cancelada pelo usuário'],
			['nome' => 'Falhada', 'descricao' => 'Transação falhada por erro'],
			['nome' => 'Estornada', 'descricao' => 'Transação estornada'],
		]);
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('status_transacao');
	}
};
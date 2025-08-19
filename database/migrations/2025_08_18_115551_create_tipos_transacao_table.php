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
		Schema::create('tipos_transacao', function (Blueprint $table) {
			$table->id();
			$table->string('nome', 30)->unique();
			$table->string('descricao', 255)->nullable();
		});

		DB::table('tipos_transacao')->insert([
			['nome' => 'Transferência', 'descricao' => 'Transferência entre contas'],
			['nome' => 'Depósito', 'descricao' => 'Depósito em conta'],
			['nome' => 'Levantamento', 'descricao' => 'Levantamento em conta'],
			['nome' => 'Pagamento', 'descricao' => 'Pagamento de serviços'],
			['nome' => 'TED', 'descricao' => 'Transferência Eletrônica Disponível'],
			['nome' => 'PIX', 'descricao' => 'Transferência instantânea PIX'],
		]);
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('tipos_transacao');
	}
};
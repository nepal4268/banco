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
		Schema::create('status_conta', function (Blueprint $table) {
			$table->id();
			$table->string('nome', 30)->unique();
			$table->string('descricao', 255)->nullable();
		});

		DB::table('status_conta')->insert([
			['nome' => 'Ativa', 'descricao' => 'Conta ativa e operacional'],
			['nome' => 'Inativa', 'descricao' => 'Conta inativa temporariamente'],
			['nome' => 'Bloqueada', 'descricao' => 'Conta bloqueada por suspeita'],
			['nome' => 'Encerrada', 'descricao' => 'Conta encerrada pelo cliente'],
			['nome' => 'Suspensa', 'descricao' => 'Conta suspensa administrativamente'],
		]);
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('status_conta');
	}
};
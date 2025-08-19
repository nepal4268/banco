<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('status_apolice', function (Blueprint $table) {
			$table->id();
			$table->string('nome', 30)->unique();
			$table->string('descricao', 255)->nullable();
		});

		DB::table('status_apolice')->insert([
			['nome' => 'Ativa', 'descricao' => 'Apólice ativa'],
			['nome' => 'Inativa', 'descricao' => 'Apólice inativa'],
			['nome' => 'Em análise', 'descricao' => 'Apólice em análise'],
			['nome' => 'Cancelada', 'descricao' => 'Apólice cancelada'],
			['nome' => 'Expirada', 'descricao' => 'Apólice expirada'],
		]);
	}

	public function down(): void
	{
		Schema::dropIfExists('status_apolice');
	}
};
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
		Schema::create('tipos_cliente', function (Blueprint $table) {
			$table->id();
			$table->string('nome', 50)->unique();
			$table->string('descricao', 255)->nullable();
		});

		DB::table('tipos_cliente')->insert([
			['nome' => 'Pessoa Física', 'descricao' => 'Cliente pessoa física individual'],
			['nome' => 'Pessoa Jurídica', 'descricao' => 'Cliente pessoa jurídica/empresa'],
			['nome' => 'VIP', 'descricao' => 'Cliente VIP com benefícios especiais'],
			['nome' => 'Corporativo', 'descricao' => 'Cliente corporativo de grande porte'],
		]);
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('tipos_cliente');
	}
};
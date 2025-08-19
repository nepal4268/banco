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
		Schema::create('moedas', function (Blueprint $table) {
			$table->id();
			$table->char('codigo', 3)->unique();
			$table->string('nome', 50);
			$table->string('simbolo', 5)->nullable();
		});

		DB::table('moedas')->insert([
			['codigo' => 'AOA', 'nome' => 'Kwanza Angolano', 'simbolo' => 'Kz'],
			['codigo' => 'USD', 'nome' => 'Dólar Americano', 'simbolo' => '$'],
			['codigo' => 'EUR', 'nome' => 'Euro', 'simbolo' => '€'],
			['codigo' => 'GBP', 'nome' => 'Libra Esterlina', 'simbolo' => '£'],
			['codigo' => 'BRL', 'nome' => 'Real Brasileiro', 'simbolo' => 'R$'],
		]);
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('moedas');
	}
};
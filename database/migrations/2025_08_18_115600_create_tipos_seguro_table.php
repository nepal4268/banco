<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('tipos_seguro', function (Blueprint $table) {
			$table->id();
			$table->string('nome', 100)->unique();
			$table->text('descricao')->nullable();
			$table->decimal('cobertura', 20, 2);
			$table->decimal('premio_mensal', 20, 2);
		});

		DB::table('tipos_seguro')->insert([
			['nome' => 'Seguro de Vida', 'descricao' => 'Cobertura para eventos de vida', 'cobertura' => '100000.00', 'premio_mensal' => '1500.00'],
			['nome' => 'Seguro de Saúde', 'descricao' => 'Cobertura de despesas médicas', 'cobertura' => '50000.00', 'premio_mensal' => '900.00'],
			['nome' => 'Seguro Automóvel', 'descricao' => 'Cobertura para veículos', 'cobertura' => '80000.00', 'premio_mensal' => '700.00'],
			['nome' => 'Seguro Residencial', 'descricao' => 'Cobertura para residências', 'cobertura' => '200000.00', 'premio_mensal' => '600.00'],
			['nome' => 'Seguro Viagem', 'descricao' => 'Cobertura em viagens', 'cobertura' => '20000.00', 'premio_mensal' => '120.00'],
		]);
	}

	public function down(): void
	{
		Schema::dropIfExists('tipos_seguro');
	}
};
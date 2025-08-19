<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('status_pagamento', function (Blueprint $table) {
			$table->id();
			$table->string('nome', 30)->unique();
			$table->string('descricao', 255)->nullable();
		});

		DB::table('status_pagamento')->insert([
			['nome' => 'Pendente', 'descricao' => 'Pagamento pendente'],
			['nome' => 'Processando', 'descricao' => 'Pagamento em processamento'],
			['nome' => 'Pago', 'descricao' => 'Pagamento concluído'],
			['nome' => 'Falhado', 'descricao' => 'Pagamento falhou'],
			['nome' => 'Estornado', 'descricao' => 'Pagamento estornado'],
			['nome' => 'Cancelado', 'descricao' => 'Pagamento cancelado'],
		]);
	}

	public function down(): void
	{
		Schema::dropIfExists('status_pagamento');
	}
};
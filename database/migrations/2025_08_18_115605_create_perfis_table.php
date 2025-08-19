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
		Schema::create('perfis', function (Blueprint $table) {
			$table->id();
			$table->string('nome', 50)->unique();
			$table->string('descricao', 255)->nullable();
			$table->timestamps();
			$table->softDeletes();
		});

		DB::table('perfis')->insert([
			['nome' => 'Administrador', 'descricao' => 'Acesso completo ao sistema bancário', 'created_at' => now(), 'updated_at' => now()],
			['nome' => 'Gerente', 'descricao' => 'Gerente de agência com acesso amplo', 'created_at' => now(), 'updated_at' => now()],
			['nome' => 'Atendente', 'descricao' => 'Atendimento ao cliente básico', 'created_at' => now(), 'updated_at' => now()],
			['nome' => 'Operador', 'descricao' => 'Operações financeiras básicas', 'created_at' => now(), 'updated_at' => now()],
		]);
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('perfis');
	}
};
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
		Schema::create('perfil_permissao', function (Blueprint $table) {
			$table->id();
			$table->foreignId('perfil_id')->constrained('perfis')->onDelete('cascade');
			$table->foreignId('permissao_id')->constrained('permissoes')->onDelete('cascade');
			$table->timestamps();
			$table->softDeletes();
		});

		$permissoesMap = DB::table('permissoes')->pluck('id', 'code');
		$perfisMap = DB::table('perfis')->pluck('id', 'nome');

		$perfilPermissoes = [
			'Administrador' => ['admin.full'],
			'Gerente' => [
				'clientes.view','clientes.create','clientes.edit',
				'contas.view','contas.create','contas.edit',
				'transacoes.view','transacoes.create','transacoes.approve',
				'relatorios.financeiro','relatorios.clientes','relatorios.transacoes'
			],
			'Atendente' => [
				'clientes.view','clientes.create','clientes.edit',
				'contas.view','contas.create',
				'transacoes.view','transacoes.create'
			],
			'Operador' => [
				'transacoes.view','transacoes.create',
				'contas.view'
			],
		];

		$rows = [];
		foreach ($perfilPermissoes as $perfilNome => $codes) {
			$perfilId = $perfisMap[$perfilNome] ?? null;
			if (!$perfilId) continue;
			foreach ($codes as $code) {
				$permId = $permissoesMap[$code] ?? null;
				if (!$permId) continue;
				$rows[] = [
					'perfil_id' => $perfilId,
					'permissao_id' => $permId,
					'created_at' => now(),
					'updated_at' => now(),
				];
			}
		}

		if (!empty($rows)) {
			DB::table('perfil_permissao')->insert($rows);
		}
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('perfil_permissao');
	}
};
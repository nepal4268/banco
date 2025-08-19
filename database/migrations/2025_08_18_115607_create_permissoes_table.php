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
		Schema::create('permissoes', function (Blueprint $table) {
			$table->id();
			$table->string('code', 100)->unique();
			$table->string('label', 150);
			$table->string('descricao', 255)->nullable();
			$table->timestamps();
			$table->softDeletes();
		});

		DB::table('permissoes')->insert([
			['code' => 'clientes.view', 'label' => 'Visualizar Clientes', 'descricao' => 'Visualizar lista e detalhes de clientes', 'created_at' => now(), 'updated_at' => now()],
			['code' => 'clientes.create', 'label' => 'Criar Clientes', 'descricao' => 'Criar novos clientes', 'created_at' => now(), 'updated_at' => now()],
			['code' => 'clientes.edit', 'label' => 'Editar Clientes', 'descricao' => 'Editar dados de clientes', 'created_at' => now(), 'updated_at' => now()],
			['code' => 'clientes.delete', 'label' => 'Excluir Clientes', 'descricao' => 'Excluir clientes do sistema', 'created_at' => now(), 'updated_at' => now()],
			['code' => 'contas.view', 'label' => 'Visualizar Contas', 'descricao' => 'Visualizar lista e detalhes de contas', 'created_at' => now(), 'updated_at' => now()],
			['code' => 'contas.create', 'label' => 'Criar Contas', 'descricao' => 'Criar novas contas bancárias', 'created_at' => now(), 'updated_at' => now()],
			['code' => 'contas.edit', 'label' => 'Editar Contas', 'descricao' => 'Editar dados de contas', 'created_at' => now(), 'updated_at' => now()],
			['code' => 'contas.delete', 'label' => 'Excluir Contas', 'descricao' => 'Excluir contas do sistema', 'created_at' => now(), 'updated_at' => now()],
			['code' => 'transacoes.view', 'label' => 'Visualizar Transações', 'descricao' => 'Visualizar histórico de transações', 'created_at' => now(), 'updated_at' => now()],
			['code' => 'transacoes.create', 'label' => 'Criar Transações', 'descricao' => 'Realizar novas transações', 'created_at' => now(), 'updated_at' => now()],
			['code' => 'transacoes.approve', 'label' => 'Aprovar Transações', 'descricao' => 'Aprovar transações pendentes', 'created_at' => now(), 'updated_at' => now()],
			['code' => 'transacoes.cancel', 'label' => 'Cancelar Transações', 'descricao' => 'Cancelar transações', 'created_at' => now(), 'updated_at' => now()],
			['code' => 'usuarios.view', 'label' => 'Visualizar Usuários', 'descricao' => 'Visualizar lista de usuários', 'created_at' => now(), 'updated_at' => now()],
			['code' => 'usuarios.create', 'label' => 'Criar Usuários', 'descricao' => 'Criar novos usuários', 'created_at' => now(), 'updated_at' => now()],
			['code' => 'usuarios.edit', 'label' => 'Editar Usuários', 'descricao' => 'Editar dados de usuários', 'created_at' => now(), 'updated_at' => now()],
			['code' => 'usuarios.delete', 'label' => 'Excluir Usuários', 'descricao' => 'Excluir usuários do sistema', 'created_at' => now(), 'updated_at' => now()],
			['code' => 'relatorios.financeiro', 'label' => 'Relatórios Financeiros', 'descricao' => 'Acessar relatórios financeiros', 'created_at' => now(), 'updated_at' => now()],
			['code' => 'relatorios.clientes', 'label' => 'Relatórios de Clientes', 'descricao' => 'Acessar relatórios de clientes', 'created_at' => now(), 'updated_at' => now()],
			['code' => 'relatorios.transacoes', 'label' => 'Relatórios de Transações', 'descricao' => 'Acessar relatórios de transações', 'created_at' => now(), 'updated_at' => now()],
			['code' => 'admin.full', 'label' => 'Administração Completa', 'descricao' => 'Acesso completo ao sistema', 'created_at' => now(), 'updated_at' => now()],
			['code' => 'admin.logs', 'label' => 'Visualizar Logs', 'descricao' => 'Visualizar logs do sistema', 'created_at' => now(), 'updated_at' => now()],
			['code' => 'admin.backup', 'label' => 'Gestão de Backup', 'descricao' => 'Realizar e restaurar backups', 'created_at' => now(), 'updated_at' => now()],
		]);
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('permissoes');
	}
};
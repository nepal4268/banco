<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // Tabelas de lookup são populadas nas migrations
		
		// 1. Estrutura organizacional
		$this->call([
			AgenciaSeeder::class,
			PermissaoSeeder::class,
			PerfilSeeder::class,
			UsuarioSeeder::class,
		]);

		// 2. Dados de clientes e contas (com relacionamentos)
		$this->call([
			ClienteSeeder::class,
			ContaSeeder::class, // Cria contas e cartões automaticamente
		]);

		// 3. Histórico e operações
		$this->call([
			TaxaCambioSeeder::class,
			TransacaoSeeder::class, // Cria histórico de transações
			PagamentoSeeder::class,
			ApoliceSeeder::class,
			SinistroSeeder::class,
			OperacaoCambioSeeder::class,
		]);

		$this->command->info('');
		$this->command->info('🎉 Sistema Bancário populado com sucesso!');
		$this->command->info('');
		$this->command->info('📊 Resumo dos dados criados:');
		$this->command->info('   🏢 Agências: ' . \App\Models\Agencia::count());
		$this->command->info('   👥 Clientes: ' . \App\Models\Cliente::count());
		$this->command->info('   💳 Contas: ' . \App\Models\Conta::count());
		$this->command->info('   🎫 Cartões: ' . \App\Models\Cartao::count());
		$this->command->info('   💸 Transações: ' . \App\Models\Transacao::count());
		$this->command->info('   👤 Usuários: ' . \App\Models\Usuario::count());
		$this->command->info('   🔐 Permissões: ' . \App\Models\Permissao::count());
		$this->command->info('');
		$this->command->info('🔑 Credenciais principais:');
		$this->command->info('   👑 Admin: admin@banco.ao / admin123');
		$this->command->info('   👔 Gerente: gerente@banco.ao / gerente123');
		$this->command->info('   👤 Atendente: atendente@banco.ao / atendente123');
		$this->command->info('   🔍 Auditor: auditor@banco.ao / auditor123');
	
    }
}
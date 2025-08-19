<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
	/**
	 * Seed the application's database.
	 */
	public function run(): void
	{
		// Tabelas de lookup agora são populadas nas migrations

		// Usuários (dependem de perfis)
		$this->call([
			UsuarioSeeder::class,
		]);

		$this->command->info('🏦 Sistema Bancário - Dados básicos inseridos com sucesso!');
		$this->command->info('👤 Usuários criados:');
		$this->command->info('   Admin: admin@banco.ao / admin123');
		$this->command->info('   Gerente: gerente@banco.ao / gerente123');
		$this->command->info('   Atendente: atendente@banco.ao / atendente123');
	}
}
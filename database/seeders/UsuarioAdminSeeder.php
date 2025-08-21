<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Perfil;
use App\Models\Agencia;

class UsuarioAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar agência padrão se não existir
        $agencia = Agencia::firstOrCreate(
            ['codigo' => 'AG001'],
            [
                'nome' => 'Agência Central',
                'endereco' => 'Rua Principal, 123',
                'telefone' => '+244 123 456 789',
                'email' => 'central@banco.ao',
                'cidade' => 'Luanda',
                'provincia' => 'Luanda',
                'descricao' => 'Agência central do sistema',
                'ativo' => true
            ]
        );

        // Obter perfil de administrador
        $perfilAdmin = Perfil::where('nome', 'Administrador')->first();

        if (!$perfilAdmin) {
            $this->command->error('Perfil Administrador não encontrado. Execute o PerfilSeeder primeiro.');
            return;
        }

        // Criar usuário administrador
        $admin = User::updateOrCreate(
            ['email' => 'admin@banco.ao'],
            [
                'nome' => 'Administrador do Sistema',
                'bi' => '1234567890123456',
                'sexo' => 'M',
                'data_nascimento' => '1990-01-01',
                'telefone' => '+244 123 456 789',
                'senha' => bcrypt('admin123'),
                'perfil_id' => $perfilAdmin->id,
                'agencia_id' => $agencia->id,
                'ativo' => true,
                'endereco' => 'Rua Principal, 123',
                'cidade' => 'Luanda',
                'provincia' => 'Luanda'
            ]
        );

        $this->command->info('✅ Usuário administrador criado/atualizado com sucesso!');
        $this->command->info('   Email: admin@banco.ao');
        $this->command->info('   Senha: admin123');
        $this->command->info('   Perfil: ' . $perfilAdmin->nome);
        $this->command->info('   Agência: ' . $agencia->nome);
    }
}
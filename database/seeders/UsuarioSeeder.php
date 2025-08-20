<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Usuario;
use App\Models\Perfil;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $perfis = [
            'Administrador' => Perfil::where('nome', 'Administrador')->first(),
            'Gerente' => Perfil::where('nome', 'Gerente')->first(),
            'Atendente' => Perfil::where('nome', 'Atendente')->first(),
            'Auditor' => Perfil::where('nome', 'Auditor')->first(),
        ];

        $usuarios = [
            [
                'nome' => 'Administrador do Sistema',
                'email' => 'admin@banco.ao',
                'senha' => 'admin123',
                'perfil' => 'Administrador',
                'agencia_id' => null, // Acesso a todas as agências
            ],
            [
                'nome' => 'João Silva',
                'email' => 'gerente@banco.ao',
                'senha' => 'gerente123',
                'perfil' => 'Gerente',
                'agencia_id' => 1, // Agência Central
            ],
            [
                'nome' => 'Maria Santos',
                'email' => 'atendente@banco.ao',
                'senha' => 'atendente123',
                'perfil' => 'Atendente',
                'agencia_id' => 1, // Agência Central
            ],
            [
                'nome' => 'Carlos Auditor',
                'email' => 'auditor@banco.ao',
                'senha' => 'auditor123',
                'perfil' => 'Auditor',
                'agencia_id' => null, // Acesso a todas as agências
            ],
            [
                'nome' => 'Ana Costa',
                'email' => 'gerente.talatona@banco.ao',
                'senha' => 'gerente123',
                'perfil' => 'Gerente',
                'agencia_id' => 2, // Agência Talatona
            ],
            [
                'nome' => 'Pedro Atendente',
                'email' => 'atendente.talatona@banco.ao',
                'senha' => 'atendente123',
                'perfil' => 'Atendente',
                'agencia_id' => 2, // Agência Talatona
            ],
            [
                'nome' => 'Luisa Mendes',
                'email' => 'gerente.benguela@banco.ao',
                'senha' => 'gerente123',
                'perfil' => 'Gerente',
                'agencia_id' => 3, // Agência Benguela
            ],
            [
                'nome' => 'Roberto Santos',
                'email' => 'atendente.benguela@banco.ao',
                'senha' => 'atendente123',
                'perfil' => 'Atendente',
                'agencia_id' => 3, // Agência Benguela
            ],
        ];

        foreach ($usuarios as $userData) {
            $perfil = $perfis[$userData['perfil']];
            
            if (!$perfil) {
                $this->command->warn("⚠️ Perfil '{$userData['perfil']}' não encontrado para usuário {$userData['nome']}");
                continue;
            }

            Usuario::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'nome' => $userData['nome'],
                    'email' => $userData['email'],
                    'senha' => bcrypt($userData['senha']),
                    'perfil_id' => $perfil->id,
                    'agencia_id' => $userData['agencia_id'],
                    'status_usuario' => 'ativo',
                    // Campos opcionais para alinhar com a migration
                    'bi' => fake()->unique()->regexify('\\d{9}[A-Z]{2}\\d{3}'),
                    'sexo' => fake()->randomElement(['M', 'F']),
                    'telefone' => [fake()->numerify('9########')],
                    'data_nascimento' => fake()->date('Y-m-d'),
                    'endereco' => fake()->streetAddress(),
                    'cidade' => fake()->city(),
                    'provincia' => fake()->state(),
                ]
            );
        }

        $this->command->info('✅ Usuários criados com sucesso! (' . Usuario::count() . ' total)');
        $this->command->info('👤 Credenciais de acesso:');
        $this->command->info('   👑 Admin: admin@banco.ao / admin123');
        $this->command->info('   👔 Gerente: gerente@banco.ao / gerente123');
        $this->command->info('   👤 Atendente: atendente@banco.ao / atendente123');
        $this->command->info('   🔍 Auditor: auditor@banco.ao / auditor123');
        $this->command->info('   📍 Outros usuários por agência também foram criados');
    }
}

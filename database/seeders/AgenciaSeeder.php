<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Agencia;

class AgenciaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $agencias = [
            [
                'codigo_banco' => '0042',
                'codigo_agencia' => '0001',
                'nome' => 'Agência Central',
                'endereco' => 'Rua Kwame Nkrumah, 123',
                'cidade' => 'Luanda',
                'provincia' => 'Luanda',
                'telefone' => '222123456',
                'email' => 'central@banco.ao',
                'gerente' => 'António Silva'
            ],
            [
                'codigo_banco' => '0042',
                'codigo_agencia' => '0002',
                'nome' => 'Agência Talatona',
                'endereco' => 'Rua da Samba, 456',
                'cidade' => 'Luanda',
                'provincia' => 'Luanda',
                'telefone' => '222234567',
                'email' => 'talatona@banco.ao',
                'gerente' => 'Maria Santos'
            ],
            [
                'codigo_banco' => '0042',
                'codigo_agencia' => '0003',
                'nome' => 'Agência Benguela',
                'endereco' => 'Rua Norton de Matos, 789',
                'cidade' => 'Benguela',
                'provincia' => 'Benguela',
                'telefone' => '272345678',
                'email' => 'benguela@banco.ao',
                'gerente' => 'João Fernandes'
            ],
            [
                'codigo_banco' => '0042',
                'codigo_agencia' => '0004',
                'nome' => 'Agência Huambo',
                'endereco' => 'Rua José Martí, 321',
                'cidade' => 'Huambo',
                'provincia' => 'Huambo',
                'telefone' => '241456789',
                'email' => 'huambo@banco.ao',
                'gerente' => 'Ana Costa'
            ],
            [
                'codigo_banco' => '0042',
                'codigo_agencia' => '0005',
                'nome' => 'Agência Lubango',
                'endereco' => 'Rua da Independência, 654',
                'cidade' => 'Lubango',
                'provincia' => 'Huíla',
                'telefone' => '261567890',
                'email' => 'lubango@banco.ao',
                'gerente' => 'Carlos Mendes'
            ]
        ];

        foreach ($agencias as $agenciaData) {
            Agencia::create($agenciaData);
        }

        $this->command->info('✅ Agências criadas com sucesso!');
    }
}
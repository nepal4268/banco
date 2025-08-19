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
                'endereco' => 'Luanda, Angola',
                'telefones' => json_encode(['972202034']),
                'email' => 'central@banco.ao',
            ],
            [
                'codigo_banco' => '0042',
                'codigo_agencia' => '0002',
                'nome' => 'Agência Talatona',
                'endereco' => 'Luanda Norte, Angola',
                'telefones' => json_encode(['963202035', '937202036']),
                'email' => 'talatona@banco.ao',
            ],
            [
                'codigo_banco' => '0042',
                'codigo_agencia' => '0003',
                'nome' => 'Agência Benguela',
                'endereco' => 'Luanda Sul, Angola',
                'telefones' => json_encode(['922202037', '930402038']),
                'email' => 'benguela@banco.ao',
            ],
            [
                'codigo_banco' => '0042',
                'codigo_agencia' => '0004',
                'nome' => 'Agência Huambo',
                'endereco' => 'Huambo, Angola',
                'telefones' => json_encode(['962202039', '962202040']),
                'email' => 'huambo@banco.ao',
            ],
            [
                'codigo_banco' => '0042',
                'codigo_agencia' => '0005',
                'nome' => 'Agência Lubango',
                'endereco' => 'Lubango, Angola',
                'telefones' => json_encode(['925202041', '943202042']),
                'email' => 'lubango@banco.ao',
            ]
        ];

        foreach ($agencias as $agenciaData) {
            Agencia::create($agenciaData);
        }

        $this->command->info('✅ Agências criadas com sucesso!');
    }
}
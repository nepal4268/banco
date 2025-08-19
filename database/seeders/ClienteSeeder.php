<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cliente;
use App\Models\TipoCliente;
use App\Models\StatusCliente;

class ClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipoCliente = TipoCliente::where('nome', 'Pessoa Física')->first() ?? TipoCliente::first();
        $statusAtivo = StatusCliente::where('nome', 'Ativo')->first() ?? StatusCliente::first();

        $clientes = [
            [
                'tipo_cliente_id' => $tipoCliente->id,
                'nome' => 'João Manuel dos Santos',
                'sexo' => 'masculino',
                'bi' => '123456789',
                'email' => 'joao.santos@email.com',
                'telefone' => '923456789',
                'data_nascimento' => '1985-03-15',
                'endereco' => 'Rua da Paz, 123, Maianga',
                'cidade' => 'Luanda',
                'provincia' => 'Luanda',
                'status_cliente_id' => $statusAtivo->id,
            ],
            [
                'tipo_cliente_id' => $tipoCliente->id,
                'nome' => 'Maria Fernanda Costa',
                'sexo' => 'feminino',
                'bi' => '234567890',
                'email' => 'maria.costa@email.com',
                'telefone' => '934567890',
                'data_nascimento' => '1990-07-22',
                'endereco' => 'Rua da Liberdade, 456, Ingombota',
                'cidade' => 'Luanda',
                'provincia' => 'Luanda',
                'status_cliente_id' => $statusAtivo->id,
            ],
            [
                'tipo_cliente_id' => $tipoCliente->id,
                'nome' => 'António Carlos Silva',
                'sexo' => 'masculino',
                'bi' => '345678901',
                'email' => 'antonio.silva@email.com',
                'telefone' => '945678901',
                'data_nascimento' => '1978-11-08',
                'endereco' => 'Rua 1º de Maio, 789, Centro',
                'cidade' => 'Benguela',
                'provincia' => 'Benguela',
                'status_cliente_id' => $statusAtivo->id,
            ],
            [
                'tipo_cliente_id' => $tipoCliente->id,
                'nome' => 'Ana Paula Mendes',
                'sexo' => 'feminino',
                'bi' => '456789012',
                'email' => 'ana.mendes@email.com',
                'telefone' => '956789012',
                'data_nascimento' => '1992-05-30',
                'endereco' => 'Rua da Esperança, 321, Centro',
                'cidade' => 'Huambo',
                'provincia' => 'Huambo',
                'status_cliente_id' => $statusAtivo->id,
            ],
            [
                'tipo_cliente_id' => $tipoCliente->id,
                'nome' => 'Pedro Miguel Fernandes',
                'sexo' => 'masculino',
                'bi' => '567890123',
                'email' => 'pedro.fernandes@email.com',
                'telefone' => '967890123',
                'data_nascimento' => '1988-12-14',
                'endereco' => 'Rua da Amizade, 654, Centro',
                'cidade' => 'Lubango',
                'provincia' => 'Huíla',
                'status_cliente_id' => $statusAtivo->id,
            ],
            [
                'tipo_cliente_id' => $tipoCliente->id,
                'nome' => 'Luisa Conceição Rodrigues',
                'sexo' => 'feminino',
                'bi' => '678901234',
                'email' => 'luisa.rodrigues@email.com',
                'telefone' => '978901234',
                'data_nascimento' => '1995-01-20',
                'endereco' => 'Rua Nova, 987, Talatona',
                'cidade' => 'Luanda',
                'provincia' => 'Luanda',
                'status_cliente_id' => $statusAtivo->id,
            ],
            [
                'tipo_cliente_id' => $tipoCliente->id,
                'nome' => 'Carlos Eduardo Pereira',
                'sexo' => 'masculino',
                'bi' => '789012345',
                'email' => 'carlos.pereira@email.com',
                'telefone' => '989012345',
                'data_nascimento' => '1983-09-05',
                'endereco' => 'Rua do Comércio, 147, Centro',
                'cidade' => 'Benguela',
                'provincia' => 'Benguela',
                'status_cliente_id' => $statusAtivo->id,
            ],
            [
                'tipo_cliente_id' => $tipoCliente->id,
                'nome' => 'Isabel Maria Gonçalves',
                'sexo' => 'feminino',
                'bi' => '890123456',
                'email' => 'isabel.goncalves@email.com',
                'telefone' => '990123456',
                'data_nascimento' => '1991-04-18',
                'endereco' => 'Rua da Vitória, 258, Centro',
                'cidade' => 'Huambo',
                'provincia' => 'Huambo',
                'status_cliente_id' => $statusAtivo->id,
            ]
        ];

        foreach ($clientes as $clienteData) {
            Cliente::create($clienteData);
        }

        // Criar alguns clientes adicionais usando factory para variedade
        Cliente::factory(12)->create();

        $this->command->info('✅ Clientes criados com sucesso! (' . Cliente::count() . ' total)');
    }
}
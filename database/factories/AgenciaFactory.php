<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Agencia;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Agencia>
 */
class AgenciaFactory extends Factory
{
    protected $model = Agencia::class;

    public function definition(): array
    {
        static $agenciaCounter = 1;
        
        $agencias = [
            ['nome' => 'Agência Central', 'endereco' => 'Rua Kwame Nkrumah, 123', 'cidade' => 'Luanda'],
            ['nome' => 'Agência Talatona', 'endereco' => 'Rua da Samba, 456', 'cidade' => 'Luanda'],
            ['nome' => 'Agência Benguela', 'endereco' => 'Rua Norton de Matos, 789', 'cidade' => 'Benguela'],
            ['nome' => 'Agência Huambo', 'endereco' => 'Rua José Martí, 321', 'cidade' => 'Huambo'],
            ['nome' => 'Agência Lubango', 'endereco' => 'Rua da Independência, 654', 'cidade' => 'Lubango'],
        ];

        $agencia = $agencias[($agenciaCounter - 1) % count($agencias)];
        $codigoAgencia = str_pad($agenciaCounter, 4, '0', STR_PAD_LEFT);
        $agenciaCounter++;

        return [
            'codigo_banco' => '0042', // Código padrão do banco
            'codigo_agencia' => $codigoAgencia,
            'nome' => $agencia['nome'],
            'endereco' => $agencia['endereco'],
            'cidade' => $agencia['cidade'],
            'provincia' => $this->getProvinciaFromCidade($agencia['cidade']),
            'telefone' => $this->faker->numerify('222######'),
            'email' => strtolower(str_replace([' ', 'ê', 'ã'], ['', 'e', 'a'], $agencia['nome'])) . '@banco.ao',
            'gerente' => $this->faker->name(),
        ];
    }

    private function getProvinciaFromCidade(string $cidade): string
    {
        $mapeamento = [
            'Luanda' => 'Luanda',
            'Benguela' => 'Benguela',
            'Huambo' => 'Huambo',
            'Lubango' => 'Huíla',
            'Cabinda' => 'Cabinda'
        ];

        return $mapeamento[$cidade] ?? 'Luanda';
    }
}
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
            ['nome' => 'Agência Central', 'endereco' => 'Luanda, Angola'],
            ['nome' => 'Agência Talatona', 'endereco' => 'Luanda Norte, Angola'],
            ['nome' => 'Agência Benguela', 'endereco' => 'Luanda Sul, Angola'],
            ['nome' => 'Agência Huambo', 'endereco' => 'Huambo, Angola'],
            ['nome' => 'Agência Lubango', 'endereco' => 'Lubango, Angola'],
        ];

        $agencia = $agencias[($agenciaCounter - 1) % count($agencias)];
        $codigoAgencia = str_pad($agenciaCounter, 4, '0', STR_PAD_LEFT);
        $agenciaCounter++;

        return [
            'codigo_banco' => '0042', // Código padrão do banco
            'codigo_agencia' => $codigoAgencia,
            'nome' => $agencia['nome'],
            'endereco' => $agencia['endereco'],
            'cidade' => $this->faker->city(),
            'provincia' => $this->faker->state(),
            'gerente' => $this->faker->name(),
            'telefone' => [$this->faker->numerify('935362625')],
            'email' => strtolower(str_replace([' ', 'ê', 'ã'], ['', 'e', 'a'], $agencia['nome'])) . '@banco.ao',
        ];
    }


}
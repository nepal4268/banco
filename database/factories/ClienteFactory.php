<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Cliente;
use App\Models\TipoCliente;
use App\Models\StatusCliente;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cliente>
 */
class ClienteFactory extends Factory
{
    protected $model = Cliente::class;

    public function definition(): array
    {
        $tipoCliente = TipoCliente::first() ?? TipoCliente::factory()->create();
        $statusCliente = StatusCliente::where('nome', 'Ativo')->first() ?? StatusCliente::first();

        return [
            'tipo_cliente_id' => $tipoCliente->id,
            'nome' => $this->faker->name(),
            'sexo' => $this->faker->randomElement(['masculino', 'feminino', 'outro']),
            'bi' => $this->faker->unique()->numerify('#########'),
            'email' => $this->faker->unique()->safeEmail(),
            'telefone' => $this->faker->numerify('9########'),
            'data_nascimento' => $this->faker->dateTimeBetween('-65 years', '-18 years')->format('Y-m-d'),
            'endereco' => $this->faker->streetAddress(),
            'cidade' => $this->faker->randomElement(['Luanda', 'Benguela', 'Huambo', 'Lubango', 'Cabinda']),
            'provincia' => $this->faker->randomElement(['Luanda', 'Benguela', 'Huambo', 'Huíla', 'Cabinda']),
            'status_cliente_id' => $statusCliente->id,
        ];
    }

    public function inativo(): static
    {
        return $this->state(function (array $attributes) {
            $statusInativo = StatusCliente::where('nome', 'Inativo')->first();
            return [
                'status_cliente_id' => $statusInativo ? $statusInativo->id : $attributes['status_cliente_id'],
            ];
        });
    }
}
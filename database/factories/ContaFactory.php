<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Conta;
use App\Models\Cliente;
use App\Models\Agencia;
use App\Models\TipoConta;
use App\Models\StatusConta;
use App\Models\Moeda;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Conta>
 */
class ContaFactory extends Factory
{
    protected $model = Conta::class;

    public function definition(): array
    {
        $cliente = Cliente::inRandomOrder()->first() ?? Cliente::factory()->create();
        $agencia = Agencia::inRandomOrder()->first() ?? Agencia::factory()->create();
        $tipoConta = TipoConta::first() ?? TipoConta::create(['nome' => 'Corrente', 'descricao' => 'Conta Corrente']);
        $statusConta = StatusConta::where('nome', 'Ativa')->first() ?? StatusConta::first();
        $moeda = Moeda::inRandomOrder()->first() ?? Moeda::where('codigo', 'AOA')->first();

        return [
            'cliente_id' => $cliente->id,
            'agencia_id' => $agencia->id,
            'tipo_conta_id' => $tipoConta->id,
            'moeda_id' => $moeda->id,
            'saldo' => $this->faker->randomFloat(2, 1000, 500000),
            'status_conta_id' => $statusConta->id,
            // numero_conta e iban são gerados automaticamente no model
        ];
    }

    public function comSaldoAlto(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'saldo' => $this->faker->randomFloat(2, 100000, 2000000),
            ];
        });
    }

    public function comSaldoBaixo(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'saldo' => $this->faker->randomFloat(2, 100, 5000),
            ];
        });
    }

    public function inativa(): static
    {
        return $this->state(function (array $attributes) {
            $statusInativa = StatusConta::where('nome', 'Inativa')->first();
            return [
                'status_conta_id' => $statusInativa ? $statusInativa->id : $attributes['status_conta_id'],
                'saldo' => 0,
            ];
        });
    }
}
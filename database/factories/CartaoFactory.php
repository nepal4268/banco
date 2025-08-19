<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Cartao;
use App\Models\Conta;
use App\Models\TipoCartao;
use App\Models\StatusCartao;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cartao>
 */
class CartaoFactory extends Factory
{
    protected $model = Cartao::class;

    public function definition(): array
    {
        $conta = Conta::inRandomOrder()->first() ?? Conta::factory()->create();
        $tipoCartao = TipoCartao::inRandomOrder()->first() ?? TipoCartao::first();
        $statusCartao = StatusCartao::where('nome', 'Ativo')->first() ?? StatusCartao::first();

        return [
            'conta_id' => $conta->id,
            'tipo_cartao_id' => $tipoCartao->id,
            'numero_cartao' => $this->generateCardNumber(),
            'validade' => $this->faker->dateTimeBetween('+1 year', '+5 years')->format('Y-m-d'),
            'limite' => $this->faker->randomFloat(2, 50000, 500000),
            'status_cartao_id' => $statusCartao->id,
        ];
    }

    private function generateCardNumber(): string
    {
        // Gerar número de cartão fictício (não real)
        $prefixes = ['4111', '4222', '5555', '5105']; // Prefixos de teste
        $prefix = $this->faker->randomElement($prefixes);
        $remaining = $this->faker->numerify('########');
        
        return $prefix . $remaining;
    }

    public function bloqueado(): static
    {
        return $this->state(function (array $attributes) {
            $statusBloqueado = StatusCartao::where('nome', 'Bloqueado')->first();
            return [
                'status_cartao_id' => $statusBloqueado ? $statusBloqueado->id : $attributes['status_cartao_id'],
            ];
        });
    }

    public function expirado(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'validade' => $this->faker->dateTimeBetween('-2 years', '-1 month')->format('Y-m-d'),
            ];
        });
    }

    public function limiteAlto(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'limite' => $this->faker->randomFloat(2, 500000, 2000000),
            ];
        });
    }
}
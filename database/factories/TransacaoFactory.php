<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Transacao;
use App\Models\Conta;
use App\Models\TipoTransacao;
use App\Models\StatusTransacao;
use App\Models\Moeda;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transacao>
 */
class TransacaoFactory extends Factory
{
    protected $model = Transacao::class;

    public function definition(): array
    {
        $tipoTransacao = TipoTransacao::inRandomOrder()->first();
        $statusTransacao = StatusTransacao::where('nome', 'Concluída')->first() ?? StatusTransacao::first();
        $moeda = Moeda::inRandomOrder()->first() ?? Moeda::where('codigo', 'AOA')->first();

        // Determinar se é transação interna ou externa
        $isInternal = $this->faker->boolean(80); // 80% das transações são internas

        if ($isInternal) {
            $contaOrigem = Conta::inRandomOrder()->first();
            $contaDestino = Conta::where('id', '!=', $contaOrigem->id)->inRandomOrder()->first();
            
            return [
                'conta_origem_id' => $contaOrigem->id,
                'conta_destino_id' => $contaDestino->id,
                'tipo_transacao_id' => $tipoTransacao->id,
                'moeda_id' => $moeda->id,
                'valor' => $this->faker->randomFloat(2, 100, 50000),
                'descricao' => $this->faker->randomElement([
                    'Transferência entre contas',
                    'Pagamento de serviços',
                    'Transferência para terceiros',
                    'Pagamento de fatura',
                    'Reembolso'
                ]),
                'status_transacao_id' => $statusTransacao->id,
                'referencia_externa' => $this->faker->optional()->regexify('[A-Z0-9]{10}'),
                'origem_externa' => false,
                'destino_externa' => false,
                'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            ];
        } else {
            // Transação externa (depósito ou levantamento)
            $conta = Conta::inRandomOrder()->first();
            $isDeposit = $this->faker->boolean();

            return [
                'conta_origem_id' => $isDeposit ? null : $conta->id,
                'conta_destino_id' => $isDeposit ? $conta->id : null,
                'tipo_transacao_id' => $tipoTransacao->id,
                'moeda_id' => $moeda->id,
                'valor' => $this->faker->randomFloat(2, 500, 100000),
                'descricao' => $isDeposit ? 'Depósito em dinheiro' : 'Levantamento em dinheiro',
                'status_transacao_id' => $statusTransacao->id,
                'referencia_externa' => $this->faker->regexify('[A-Z0-9]{12}'),
                'origem_externa' => !$isDeposit,
                'destino_externa' => $isDeposit,
                'conta_externa_origem' => $isDeposit ? null : 'Caixa Automático',
                'conta_externa_destino' => $isDeposit ? 'Depósito Balcão' : null,
                'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            ];
        }
    }

    public function deposito(): static
    {
        return $this->state(function (array $attributes) {
            $conta = Conta::inRandomOrder()->first();
            $tipoDeposito = TipoTransacao::where('nome', 'Depósito')->first() ?? TipoTransacao::first();
            
            return [
                'conta_origem_id' => null,
                'conta_destino_id' => $conta->id,
                'tipo_transacao_id' => $tipoDeposito->id,
                'descricao' => 'Depósito em dinheiro',
                'origem_externa' => true,
                'destino_externa' => false,
                'conta_externa_origem' => 'Depósito Balcão',
                'conta_externa_destino' => null,
            ];
        });
    }

    public function levantamento(): static
    {
        return $this->state(function (array $attributes) {
            $conta = Conta::inRandomOrder()->first();
            $tipoLevantamento = TipoTransacao::where('nome', 'Levantamento')->first() ?? TipoTransacao::first();
            
            return [
                'conta_origem_id' => $conta->id,
                'conta_destino_id' => null,
                'tipo_transacao_id' => $tipoLevantamento->id,
                'descricao' => 'Levantamento em dinheiro',
                'origem_externa' => false,
                'destino_externa' => true,
                'conta_externa_origem' => null,
                'conta_externa_destino' => 'Caixa Automático',
            ];
        });
    }

    public function transferencia(): static
    {
        return $this->state(function (array $attributes) {
            $contaOrigem = Conta::inRandomOrder()->first();
            $contaDestino = Conta::where('id', '!=', $contaOrigem->id)->inRandomOrder()->first();
            $tipoTransferencia = TipoTransacao::where('nome', 'Transferência')->first() ?? TipoTransacao::first();
            
            return [
                'conta_origem_id' => $contaOrigem->id,
                'conta_destino_id' => $contaDestino->id,
                'tipo_transacao_id' => $tipoTransferencia->id,
                'descricao' => 'Transferência entre contas',
                'origem_externa' => false,
                'destino_externa' => false,
            ];
        });
    }
}
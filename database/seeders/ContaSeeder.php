<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Conta;
use App\Models\Cliente;
use App\Models\Agencia;
use App\Models\TipoConta;
use App\Models\StatusConta;
use App\Models\Moeda;
use App\Models\Cartao;
use App\Models\TipoCartao;
use App\Models\StatusCartao;

class ContaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clientes = Cliente::all();
        $agencias = Agencia::all();
        $tipoConta = TipoConta::where('nome', 'Corrente')->first() ?? TipoConta::first();
        $statusAtiva = StatusConta::where('nome', 'Ativa')->first() ?? StatusConta::first();
        $moedaAOA = Moeda::where('codigo', 'AOA')->first();
        $moedaUSD = Moeda::where('codigo', 'USD')->first();
        $moedaEUR = Moeda::where('codigo', 'EUR')->first();

        // Garantir que cada cliente tenha pelo menos uma conta em AOA
        foreach ($clientes as $cliente) {
            $agencia = $agencias->random();
            
            // Conta principal em AOA
            $contaAOA = Conta::create([
                'cliente_id' => $cliente->id,
                'agencia_id' => $agencia->id,
                'tipo_conta_id' => $tipoConta->id,
                'moeda_id' => $moedaAOA->id,
                'saldo' => fake()->randomFloat(2, 50000, 2000000), // Saldo entre 50k e 2M AOA
                'status_conta_id' => $statusAtiva->id,
            ]);

            // Criar cartão para a conta AOA
            $this->criarCartaoParaConta($contaAOA);

            // 70% dos clientes também têm conta em USD
            if (fake()->boolean(70) && $moedaUSD) {
                $contaUSD = Conta::create([
                    'cliente_id' => $cliente->id,
                    'agencia_id' => $agencia->id,
                    'tipo_conta_id' => $tipoConta->id,
                    'moeda_id' => $moedaUSD->id,
                    'saldo' => fake()->randomFloat(2, 500, 50000), // Saldo entre $500 e $50k
                    'status_conta_id' => $statusAtiva->id,
                ]);

                // 50% das contas USD também têm cartão
                if (fake()->boolean(50)) {
                    $this->criarCartaoParaConta($contaUSD);
                }
            }

            // 30% dos clientes têm conta em EUR
            if (fake()->boolean(30) && $moedaEUR) {
                $contaEUR = Conta::create([
                    'cliente_id' => $cliente->id,
                    'agencia_id' => $agencia->id,
                    'tipo_conta_id' => $tipoConta->id,
                    'moeda_id' => $moedaEUR->id,
                    'saldo' => fake()->randomFloat(2, 500, 30000), // Saldo entre €500 e €30k
                    'status_conta_id' => $statusAtiva->id,
                ]);
            }
        }

        // Criar algumas contas adicionais usando factory para variedade
        Conta::factory(15)->create();

        $this->command->info('✅ Contas criadas com sucesso! (' . Conta::count() . ' total)');
        $this->command->info('✅ Cartões criados com sucesso! (' . Cartao::count() . ' total)');
    }

    private function criarCartaoParaConta(Conta $conta): void
    {
        $tipoCartao = TipoCartao::inRandomOrder()->first() ?? TipoCartao::first();
        $statusCartao = StatusCartao::where('nome', 'Ativo')->first() ?? StatusCartao::first();

        // Definir limite baseado na moeda
        $limite = match($conta->moeda->codigo) {
            'AOA' => fake()->randomFloat(2, 100000, 1000000), // 100k a 1M AOA
            'USD' => fake()->randomFloat(2, 1000, 10000),     // $1k a $10k
            'EUR' => fake()->randomFloat(2, 1000, 8000),      // €1k a €8k
            default => 50000
        };

        Cartao::create([
            'conta_id' => $conta->id,
            'tipo_cartao_id' => $tipoCartao->id,
            'numero_cartao' => $this->gerarNumeroCartao(),
            'validade' => fake()->dateTimeBetween('+1 year', '+4 years')->format('Y-m-d'),
            'limite' => $limite,
            'status_cartao_id' => $statusCartao->id,
        ]);
    }

    private function gerarNumeroCartao(): string
    {
        // Prefixos seguros para teste
        $prefixes = ['4111', '4222', '5555', '5105'];
        $prefix = fake()->randomElement($prefixes);
        $remaining = fake()->numerify('########');
        
        return $prefix . $remaining;
    }
}
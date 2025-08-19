<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Transacao;
use App\Models\Conta;
use App\Models\TipoTransacao;
use App\Models\StatusTransacao;
use App\Models\Moeda;
use Carbon\Carbon;

class TransacaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contas = Conta::all();
        $statusConcluida = StatusTransacao::where('nome', 'Concluída')->first() ?? StatusTransacao::first();
        $statusPendente = StatusTransacao::where('nome', 'Pendente')->first() ?? $statusConcluida;
        
        $tipoDeposito = TipoTransacao::where('nome', 'Depósito')->first() ?? TipoTransacao::first();
        $tipoLevantamento = TipoTransacao::where('nome', 'Levantamento')->first() ?? TipoTransacao::first();
        $tipoTransferencia = TipoTransacao::where('nome', 'Transferência')->first() ?? TipoTransacao::first();

        $transacoesCriadas = 0;

        // Para cada conta, criar histórico de transações dos últimos 6 meses
        foreach ($contas as $conta) {
            $this->criarHistoricoParaConta($conta, $tipoDeposito, $tipoLevantamento, $tipoTransferencia, $statusConcluida, $statusPendente, $transacoesCriadas);
        }

        // Criar algumas transações usando factory para variedade adicional
        Transacao::factory(200)->create();
        $transacoesCriadas += 200;

        $this->command->info("✅ Transações criadas com sucesso! ($transacoesCriadas total)");
    }

    private function criarHistoricoParaConta($conta, $tipoDeposito, $tipoLevantamento, $tipoTransferencia, $statusConcluida, $statusPendente, &$transacoesCriadas): void
    {
        $dataInicio = Carbon::now()->subMonths(6);
        $dataFim = Carbon::now();

        // Criar depósito inicial (abertura da conta)
        Transacao::create([
            'conta_origem_id' => null,
            'conta_destino_id' => $conta->id,
            'tipo_transacao_id' => $tipoDeposito->id,
            'moeda_id' => $conta->moeda_id,
            'valor' => fake()->randomFloat(2, 10000, 100000),
            'descricao' => 'Depósito inicial - Abertura de conta',
            'status_transacao_id' => $statusConcluida->id,
            'referencia_externa' => 'DEP-' . fake()->regexify('[A-Z0-9]{8}'),
            'origem_externa' => true,
            'destino_externa' => false,
            'conta_externa_origem' => 'Depósito Balcão',
            'created_at' => $dataInicio->copy()->addDays(fake()->numberBetween(0, 7)),
        ]);
        $transacoesCriadas++;

        // Criar transações mensais
        for ($mes = 0; $mes < 6; $mes++) {
            $dataBase = $dataInicio->copy()->addMonths($mes);
            $quantidadeTransacoesMes = fake()->numberBetween(3, 15);

            for ($i = 0; $i < $quantidadeTransacoesMes; $i++) {
                $dataTransacao = $dataBase->copy()->addDays(fake()->numberBetween(0, 28));
                
                $tipoTransacao = fake()->randomElement([
                    ['tipo' => 'deposito', 'peso' => 20],
                    ['tipo' => 'levantamento', 'peso' => 25],
                    ['tipo' => 'transferencia_enviada', 'peso' => 30],
                    ['tipo' => 'transferencia_recebida', 'peso' => 25]
                ]);

                switch ($tipoTransacao['tipo']) {
                    case 'deposito':
                        $this->criarDeposito($conta, $tipoDeposito, $statusConcluida, $dataTransacao, $transacoesCriadas);
                        break;
                    
                    case 'levantamento':
                        $this->criarLevantamento($conta, $tipoLevantamento, $statusConcluida, $dataTransacao, $transacoesCriadas);
                        break;
                    
                    case 'transferencia_enviada':
                        $this->criarTransferenciaEnviada($conta, $tipoTransferencia, $statusConcluida, $dataTransacao, $transacoesCriadas);
                        break;
                    
                    case 'transferencia_recebida':
                        $this->criarTransferenciaRecebida($conta, $tipoTransferencia, $statusConcluida, $dataTransacao, $transacoesCriadas);
                        break;
                }
            }
        }

        // Criar algumas transações pendentes (5% do total)
        if (fake()->boolean(20)) {
            $this->criarTransacaoPendente($conta, $tipoTransferencia, $statusPendente, $transacoesCriadas);
        }
    }

    private function criarDeposito($conta, $tipoDeposito, $status, $data, &$contador): void
    {
        Transacao::create([
            'conta_origem_id' => null,
            'conta_destino_id' => $conta->id,
            'tipo_transacao_id' => $tipoDeposito->id,
            'moeda_id' => $conta->moeda_id,
            'valor' => $this->gerarValorPorMoeda($conta->moeda->codigo, 'deposito'),
            'descricao' => fake()->randomElement([
                'Depósito em dinheiro',
                'Depósito via transferência bancária',
                'Depósito salário',
                'Depósito rendimentos'
            ]),
            'status_transacao_id' => $status->id,
            'referencia_externa' => 'DEP-' . fake()->regexify('[A-Z0-9]{8}'),
            'origem_externa' => true,
            'destino_externa' => false,
            'conta_externa_origem' => fake()->randomElement(['Depósito Balcão', 'Transferência Bancária', 'Depósito ATM']),
            'created_at' => $data,
        ]);
        $contador++;
    }

    private function criarLevantamento($conta, $tipoLevantamento, $status, $data, &$contador): void
    {
        Transacao::create([
            'conta_origem_id' => $conta->id,
            'conta_destino_id' => null,
            'tipo_transacao_id' => $tipoLevantamento->id,
            'moeda_id' => $conta->moeda_id,
            'valor' => $this->gerarValorPorMoeda($conta->moeda->codigo, 'levantamento'),
            'descricao' => fake()->randomElement([
                'Levantamento em dinheiro',
                'Levantamento ATM',
                'Levantamento balcão'
            ]),
            'status_transacao_id' => $status->id,
            'referencia_externa' => 'LEV-' . fake()->regexify('[A-Z0-9]{8}'),
            'origem_externa' => false,
            'destino_externa' => true,
            'conta_externa_destino' => fake()->randomElement(['Caixa Automático', 'Levantamento Balcão']),
            'created_at' => $data,
        ]);
        $contador++;
    }

    private function criarTransferenciaEnviada($contaOrigem, $tipoTransferencia, $status, $data, &$contador): void
    {
        $contasDestino = Conta::where('id', '!=', $contaOrigem->id)
            ->where('moeda_id', $contaOrigem->moeda_id)
            ->get();
        
        if ($contasDestino->isEmpty()) return;

        $contaDestino = $contasDestino->random();

        Transacao::create([
            'conta_origem_id' => $contaOrigem->id,
            'conta_destino_id' => $contaDestino->id,
            'tipo_transacao_id' => $tipoTransferencia->id,
            'moeda_id' => $contaOrigem->moeda_id,
            'valor' => $this->gerarValorPorMoeda($contaOrigem->moeda->codigo, 'transferencia'),
            'descricao' => fake()->randomElement([
                'Transferência para terceiros',
                'Pagamento de serviços',
                'Pagamento de fatura',
                'Transferência familiar',
                'Pagamento fornecedor'
            ]),
            'status_transacao_id' => $status->id,
            'referencia_externa' => 'TRF-' . fake()->regexify('[A-Z0-9]{8}'),
            'origem_externa' => false,
            'destino_externa' => false,
            'created_at' => $data,
        ]);
        $contador++;
    }

    private function criarTransferenciaRecebida($contaDestino, $tipoTransferencia, $status, $data, &$contador): void
    {
        $contasOrigem = Conta::where('id', '!=', $contaDestino->id)
            ->where('moeda_id', $contaDestino->moeda_id)
            ->get();
        
        if ($contasOrigem->isEmpty()) return;

        $contaOrigem = $contasOrigem->random();

        Transacao::create([
            'conta_origem_id' => $contaOrigem->id,
            'conta_destino_id' => $contaDestino->id,
            'tipo_transacao_id' => $tipoTransferencia->id,
            'moeda_id' => $contaDestino->moeda_id,
            'valor' => $this->gerarValorPorMoeda($contaDestino->moeda->codigo, 'transferencia'),
            'descricao' => fake()->randomElement([
                'Transferência recebida',
                'Pagamento recebido',
                'Reembolso',
                'Transferência familiar recebida'
            ]),
            'status_transacao_id' => $status->id,
            'referencia_externa' => 'TRF-' . fake()->regexify('[A-Z0-9]{8}'),
            'origem_externa' => false,
            'destino_externa' => false,
            'created_at' => $data,
        ]);
        $contador++;
    }

    private function criarTransacaoPendente($conta, $tipoTransferencia, $statusPendente, &$contador): void
    {
        $contasDestino = Conta::where('id', '!=', $conta->id)
            ->where('moeda_id', $conta->moeda_id)
            ->get();
        
        if ($contasDestino->isEmpty()) return;

        $contaDestino = $contasDestino->random();

        Transacao::create([
            'conta_origem_id' => $conta->id,
            'conta_destino_id' => $contaDestino->id,
            'tipo_transacao_id' => $tipoTransferencia->id,
            'moeda_id' => $conta->moeda_id,
            'valor' => $this->gerarValorPorMoeda($conta->moeda->codigo, 'transferencia'),
            'descricao' => 'Transferência pendente de aprovação',
            'status_transacao_id' => $statusPendente->id,
            'referencia_externa' => 'PEN-' . fake()->regexify('[A-Z0-9]{8}'),
            'origem_externa' => false,
            'destino_externa' => false,
            'created_at' => Carbon::now()->subHours(fake()->numberBetween(1, 48)),
        ]);
        $contador++;
    }

    private function gerarValorPorMoeda(string $codigoMoeda, string $tipoOperacao): float
    {
        return match([$codigoMoeda, $tipoOperacao]) {
            ['AOA', 'deposito'] => fake()->randomFloat(2, 5000, 500000),
            ['AOA', 'levantamento'] => fake()->randomFloat(2, 2000, 200000),
            ['AOA', 'transferencia'] => fake()->randomFloat(2, 1000, 1000000),
            
            ['USD', 'deposito'] => fake()->randomFloat(2, 50, 5000),
            ['USD', 'levantamento'] => fake()->randomFloat(2, 20, 2000),
            ['USD', 'transferencia'] => fake()->randomFloat(2, 10, 10000),
            
            ['EUR', 'deposito'] => fake()->randomFloat(2, 50, 4000),
            ['EUR', 'levantamento'] => fake()->randomFloat(2, 20, 1500),
            ['EUR', 'transferencia'] => fake()->randomFloat(2, 10, 8000),
            
            default => fake()->randomFloat(2, 100, 10000),
        };
    }
}
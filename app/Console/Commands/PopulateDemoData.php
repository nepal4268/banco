<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\{Cliente, Conta, Agencia, Moeda, TipoConta, StatusConta, Usuario, Perfil, Cartao, TipoCartao, StatusCartao};
use Illuminate\Support\Str;

class PopulateDemoData extends Command
{
    protected $signature = 'demo:populate {--fresh : Limpar tabelas antes de popular}';
    protected $description = 'Popula o banco de dados com dados de exemplo coerentes';

    public function handle(): int
    {
        DB::beginTransaction();
        try {
            if ($this->option('fresh')) {
                $this->truncateAll();
            }

            $this->info('Criando clientes...');
            $clientes = Cliente::factory()->count(5)->create();

            $this->info('Buscando tabelas de apoio...');
            $agencia = Agencia::first();
            $moedas = Moeda::take(2)->get();
            $tipoConta = TipoConta::first();
            $statusAtiva = StatusConta::where('nome', 'Ativa')->first() ?? StatusConta::first();

            $this->info('Criando contas...');
            $contas = collect();
            foreach ($clientes as $cliente) {
                foreach ($moedas as $moeda) {
                    $contas->push(Conta::create([
                        'cliente_id' => $cliente->id,
                        'agencia_id' => $agencia->id,
                        'tipo_conta_id' => $tipoConta->id,
                        'moeda_id' => $moeda->id,
                        'saldo' => 10000,
                        'status_conta_id' => $statusAtiva->id,
                    ]));
                }
            }

            $this->info('Criando cartões...');
            $tipoCartao = TipoCartao::first();
            $statusCartao = StatusCartao::first();
            foreach ($contas as $conta) {
                Cartao::create([
                    'conta_id' => $conta->id,
                    'tipo_cartao_id' => $tipoCartao->id,
                    'numero_cartao' => '4111111111111111',
                    'validade' => now()->addYears(3)->toDateString(),
                    'status_cartao_id' => $statusCartao->id,
                ]);
            }

            DB::commit();
            $this->info('Dados de exemplo populados com sucesso.');
            return self::SUCCESS;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Erro ao popular: ' . $e->getMessage());
            return self::FAILURE;
        }
    }

    private function truncateAll(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        foreach (['cartoes','contas','clientes'] as $table) {
            DB::table($table)->truncate();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}


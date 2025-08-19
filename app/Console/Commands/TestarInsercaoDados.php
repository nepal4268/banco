<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\{
    Agencia, Cliente, Usuario, Conta, Cartao, Transacao, 
    OperacaoCambio, TaxaCambio, Apolice, Sinistro, 
    Pagamento, LogAcao, Perfil, Permissao
};
use Illuminate\Support\Facades\DB;
use Exception;

class TestarInsercaoDados extends Command
{
    protected $signature = 'test:insercao-dados {--fresh : Recriar banco antes do teste}';
    protected $description = 'Testa inserção de dados em todas as tabelas principais (não lookup)';

    private $sucessos = 0;
    private $erros = 0;

    public function handle(): int
    {
        $this->info('🧪 Iniciando testes de inserção de dados...');
        $this->newLine();

        if ($this->option('fresh')) {
            $this->info('🔄 Recriando banco de dados...');
            $this->call('migrate:fresh', ['--seed' => true]);
            $this->newLine();
        }

        // Testar inserção em cada tabela
        $this->testarAgencias();
        $this->testarPerfisPermissoes();
        $this->testarUsuarios();
        $this->testarClientes();
        $this->testarContas();
        $this->testarCartoes();
        $this->testarTaxasCambio();
        $this->testarTransacoes();
        $this->testarOperacoesCambio();
        $this->testarApolices();
        $this->testarSinistros();
        $this->testarPagamentos();
        $this->testarLogsAcao();

        $this->newLine();
        $this->info("📊 Resumo dos testes:");
        $this->info("   ✅ Sucessos: {$this->sucessos}");
        $this->info("   ❌ Erros: {$this->erros}");

        if ($this->erros === 0) {
            $this->info('🎉 Todos os testes de inserção passaram!');
            return self::SUCCESS;
        } else {
            $this->error('❌ Alguns testes falharam. Verifique os erros acima.');
            return self::FAILURE;
        }
    }

    private function testarAgencias(): void
    {
        $this->info('🏢 Testando inserção de Agências...');

        try {
            $agencia = Agencia::create([
                'codigo_banco' => '0042',
                'codigo_agencia' => '9999',
                'nome' => 'Agência Teste',
                'endereco' => 'Rua de Teste, 123, Luanda',
                'telefones' => ['930000001', '222000001'],
                'email' => 'teste@banco.ao',
                'ativa' => true,
            ]);

            $this->line("   ✅ Agência criada: ID {$agencia->id} - {$agencia->nome}");
            $this->line("   📞 Telefones: " . implode(', ', $agencia->telefones));
            $this->line("   🏢 Código: {$agencia->codigo_banco}{$agencia->codigo_agencia}");
            $this->sucessos++;

            // Limpar dados de teste
            $agencia->delete();
        } catch (Exception $e) {
            $this->error("   ❌ Erro ao criar agência: " . $e->getMessage());
            $this->erros++;
        }
    }

    private function testarPerfisPermissoes(): void
    {
        $this->info('🔐 Testando inserção de Perfis e Permissões...');

        try {
            // Testar perfil
            $perfil = Perfil::create([
                'nome' => 'Perfil Teste',
                'descricao' => 'Perfil para testes automatizados',
                'ativo' => true
            ]);

            $this->line("   ✅ Perfil criado: ID {$perfil->id} - {$perfil->nome}");
            $this->sucessos++;

            // Testar permissão
            $permissao = Permissao::create([
                'code' => 'teste.action',
                'label' => 'Ação de Teste',
                'descricao' => 'Permissão para testes automatizados'
            ]);

            $this->line("   ✅ Permissão criada: ID {$permissao->id} - {$permissao->code}");
            $this->sucessos++;

            // Testar relacionamento
            $perfil->permissoes()->attach($permissao->id);
            $this->line("   ✅ Relacionamento perfil-permissão criado");
            $this->sucessos++;

            // Limpar
            $perfil->permissoes()->detach();
            $perfil->delete();
            $permissao->delete();
        } catch (Exception $e) {
            $this->error("   ❌ Erro ao criar perfil/permissão: " . $e->getMessage());
            $this->erros++;
        }
    }

    private function testarUsuarios(): void
    {
        $this->info('👤 Testando inserção de Usuários...');

        try {
            $perfil = Perfil::first();
            if (!$perfil) {
                throw new Exception('Nenhum perfil encontrado');
            }

            $usuario = Usuario::create([
                'nome' => 'Usuário Teste',
                'email' => 'teste@teste.com',
                'senha' => bcrypt('teste123'),
                'perfil_id' => $perfil->id,
                'status_usuario' => 'ativo'
            ]);

            $this->line("   ✅ Usuário criado: ID {$usuario->id} - {$usuario->nome}");
            $this->line("   📧 Email: {$usuario->email}");
            $this->line("   👔 Perfil: {$usuario->perfil->nome}");
            $this->sucessos++;

            // Limpar
            $usuario->delete();
        } catch (Exception $e) {
            $this->error("   ❌ Erro ao criar usuário: " . $e->getMessage());
            $this->erros++;
        }
    }

    private function testarClientes(): void
    {
        $this->info('👥 Testando inserção de Clientes...');

        try {
            $tipoCliente = \App\Models\TipoCliente::first();
            $statusCliente = \App\Models\StatusCliente::first();

            if (!$tipoCliente || !$statusCliente) {
                throw new Exception('Tipos ou status de cliente não encontrados');
            }

            $bi = $this->gerarBI();
            $cliente = Cliente::create([
                'nome' => 'Cliente Teste',
                'sexo' => 'M',
                'bi' => $bi,
                'tipo_cliente_id' => $tipoCliente->id,
                'status_cliente_id' => $statusCliente->id,
            ]);

            $this->line("   ✅ Cliente criado: ID {$cliente->id} - {$cliente->nome}");
            $this->line("   🆔 BI: {$cliente->bi}");
            $this->line("   👤 Sexo: {$cliente->sexo}");
            
            // Validar formato do BI
            if (preg_match('/^\d{9}[A-Z]{2}\d{3}$/', $cliente->bi)) {
                $this->line("   ✅ Formato BI válido");
            } else {
                $this->error("   ❌ Formato BI inválido");
            }
            
            $this->sucessos++;

            // Limpar
            $cliente->delete();
        } catch (Exception $e) {
            $this->error("   ❌ Erro ao criar cliente: " . $e->getMessage());
            $this->erros++;
        }
    }

    private function testarContas(): void
    {
        $this->info('💳 Testando inserção de Contas...');

        try {
            $cliente = Cliente::first();
            $agencia = Agencia::first();
            $tipoConta = \App\Models\TipoConta::first();
            $statusConta = \App\Models\StatusConta::first();
            $moeda = \App\Models\Moeda::first();

            if (!$cliente || !$agencia || !$tipoConta || !$statusConta || !$moeda) {
                throw new Exception('Dados necessários não encontrados');
            }

            $conta = Conta::create([
                'cliente_id' => $cliente->id,
                'agencia_id' => $agencia->id,
                'tipo_conta_id' => $tipoConta->id,
                'moeda_id' => $moeda->id,
                'saldo' => 100000.50,
                'status_conta_id' => $statusConta->id,
            ]);

            $this->line("   ✅ Conta criada: ID {$conta->id}");
            $this->line("   🏦 Número: {$conta->numero_conta}");
            $this->line("   🌍 IBAN: {$conta->iban}");
            $this->line("   💰 Saldo: {$conta->saldo} {$conta->moeda->codigo}");
            $this->line("   👤 Cliente: {$conta->cliente->nome}");
            $this->sucessos++;

            // Limpar
            $conta->delete();
        } catch (Exception $e) {
            $this->error("   ❌ Erro ao criar conta: " . $e->getMessage());
            $this->erros++;
        }
    }

    private function testarCartoes(): void
    {
        $this->info('🎫 Testando inserção de Cartões...');

        try {
            $conta = Conta::first();
            $tipoCartao = \App\Models\TipoCartao::first();
            $statusCartao = \App\Models\StatusCartao::first();

            if (!$conta || !$tipoCartao || !$statusCartao) {
                throw new Exception('Dados necessários não encontrados');
            }

            $numeroCartao = $this->gerarNumeroCartao($conta);
            
            $cartao = Cartao::create([
                'conta_id' => $conta->id,
                'tipo_cartao_id' => $tipoCartao->id,
                'numero_cartao' => $numeroCartao,
                'validade' => now()->addYears(3)->format('Y-m-d'),
                'limite' => 500000.00,
                'status_cartao_id' => $statusCartao->id,
            ]);

            $this->line("   ✅ Cartão criado: ID {$cartao->id}");
            $this->line("   💳 Número: {$cartao->numero_cartao}");
            $this->line("   📅 Validade: {$cartao->validade}");
            $this->line("   💰 Limite: {$cartao->limite}");
            
            // Validar formato do cartão
            if (preg_match('/^4042\d{12}$/', $cartao->numero_cartao)) {
                $this->line("   ✅ Formato cartão válido (4042XXXXXXXXXXXX)");
            } else {
                $this->error("   ❌ Formato cartão inválido");
            }
            
            $this->sucessos++;

            // Limpar
            $cartao->delete();
        } catch (Exception $e) {
            $this->error("   ❌ Erro ao criar cartão: " . $e->getMessage());
            $this->erros++;
        }
    }

    private function testarTaxasCambio(): void
    {
        $this->info('💱 Testando inserção de Taxas de Câmbio...');

        try {
            $moedaAOA = \App\Models\Moeda::where('codigo', 'AOA')->first();
            $moedaUSD = \App\Models\Moeda::where('codigo', 'USD')->first();

            if (!$moedaAOA || !$moedaUSD) {
                throw new Exception('Moedas AOA e USD não encontradas');
            }

            $taxa = TaxaCambio::create([
                'moeda_origem_id' => $moedaUSD->id,
                'moeda_destino_id' => $moedaAOA->id,
                'taxa_compra' => 825.50,
                'taxa_venda' => 830.00,
                'ativa' => true,
            ]);

            $this->line("   ✅ Taxa de câmbio criada: ID {$taxa->id}");
            $this->line("   💱 {$moedaUSD->codigo} → {$moedaAOA->codigo}");
            $this->line("   📈 Compra: {$taxa->taxa_compra}");
            $this->line("   📉 Venda: {$taxa->taxa_venda}");
            $this->sucessos++;

            // Limpar
            $taxa->delete();
        } catch (Exception $e) {
            $this->error("   ❌ Erro ao criar taxa de câmbio: " . $e->getMessage());
            $this->erros++;
        }
    }

    private function testarTransacoes(): void
    {
        $this->info('💸 Testando inserção de Transações...');

        try {
            $contaOrigem = Conta::first();
            $contaDestino = Conta::skip(1)->first();
            $tipoTransacao = \App\Models\TipoTransacao::first();
            $statusTransacao = \App\Models\StatusTransacao::first();
            $moeda = \App\Models\Moeda::first();

            if (!$contaOrigem || !$contaDestino || !$tipoTransacao || !$statusTransacao || !$moeda) {
                throw new Exception('Dados necessários não encontrados');
            }

            $transacao = Transacao::create([
                'conta_origem_id' => $contaOrigem->id,
                'conta_destino_id' => $contaDestino->id,
                'tipo_transacao_id' => $tipoTransacao->id,
                'moeda_id' => $moeda->id,
                'valor' => 50000.00,
                'descricao' => 'Transação de teste',
                'status_transacao_id' => $statusTransacao->id,
                'referencia_externa' => 'TEST-' . uniqid(),
                'origem_externa' => false,
                'destino_externa' => false,
            ]);

            $this->line("   ✅ Transação criada: ID {$transacao->id}");
            $this->line("   💰 Valor: {$transacao->valor} {$moeda->codigo}");
            $this->line("   🏦 Origem: Conta {$contaOrigem->numero_conta}");
            $this->line("   🏦 Destino: Conta {$contaDestino->numero_conta}");
            $this->line("   📝 Descrição: {$transacao->descricao}");
            $this->sucessos++;

            // Limpar
            $transacao->delete();
        } catch (Exception $e) {
            $this->error("   ❌ Erro ao criar transação: " . $e->getMessage());
            $this->erros++;
        }
    }

    private function testarOperacoesCambio(): void
    {
        $this->info('🔄 Testando inserção de Operações de Câmbio...');

        try {
            $contaAOA = Conta::whereHas('moeda', fn($q) => $q->where('codigo', 'AOA'))->first();
            $contaUSD = Conta::whereHas('moeda', fn($q) => $q->where('codigo', 'USD'))->first();
            $moedaAOA = \App\Models\Moeda::where('codigo', 'AOA')->first();
            $moedaUSD = \App\Models\Moeda::where('codigo', 'USD')->first();

            if (!$contaAOA || !$contaUSD || !$moedaAOA || !$moedaUSD) {
                // Criar contas temporárias se necessário
                $this->line("   ⚠️ Criando dados temporários para teste...");
                
                $agencia = Agencia::first();
                $cliente = Cliente::first();
                $tipoConta = \App\Models\TipoConta::first();
                $statusConta = \App\Models\StatusConta::first();

                if (!$contaUSD && $moedaUSD) {
                    $contaUSD = Conta::create([
                        'cliente_id' => $cliente->id,
                        'agencia_id' => $agencia->id,
                        'tipo_conta_id' => $tipoConta->id,
                        'moeda_id' => $moedaUSD->id,
                        'saldo' => 5000.00,
                        'status_conta_id' => $statusConta->id,
                    ]);
                }
            }

            if ($contaAOA && $contaUSD && $moedaAOA && $moedaUSD) {
                $operacao = OperacaoCambio::create([
                    'conta_origem_id' => $contaUSD->id,
                    'conta_destino_id' => $contaAOA->id,
                    'moeda_origem_id' => $moedaUSD->id,
                    'moeda_destino_id' => $moedaAOA->id,
                    'valor_origem' => 100.00,
                    'taxa_aplicada' => 830.00,
                    'valor_destino' => 83000.00,
                ]);

                $this->line("   ✅ Operação de câmbio criada: ID {$operacao->id}");
                $this->line("   💱 {$moedaUSD->codigo} → {$moedaAOA->codigo}");
                $this->line("   💰 Valor origem: {$operacao->valor_origem} USD");
                $this->line("   📈 Taxa: {$operacao->taxa_aplicada}");
                $this->line("   💰 Valor destino: {$operacao->valor_destino} AOA");
                $this->sucessos++;

                // Limpar
                $operacao->delete();
            } else {
                throw new Exception('Não foi possível criar dados necessários para teste');
            }
        } catch (Exception $e) {
            $this->error("   ❌ Erro ao criar operação de câmbio: " . $e->getMessage());
            $this->erros++;
        }
    }

    private function testarApolices(): void
    {
        $this->info('🛡️ Testando inserção de Apólices...');

        try {
            $cliente = Cliente::first();
            $tipoSeguro = \App\Models\TipoSeguro::first();
            $statusApolice = \App\Models\StatusApolice::first();

            if (!$cliente || !$tipoSeguro || !$statusApolice) {
                throw new Exception('Dados necessários não encontrados');
            }

            $apolice = Apolice::create([
                'cliente_id' => $cliente->id,
                'tipo_seguro_id' => $tipoSeguro->id,
                'numero_apolice' => 'AP-' . uniqid(),
                'valor_segurado' => 500000.00,
                'premio' => 25000.00,
                'data_inicio' => now()->format('Y-m-d'),
                'data_fim' => now()->addYear()->format('Y-m-d'),
                'status_apolice_id' => $statusApolice->id,
            ]);

            $this->line("   ✅ Apólice criada: ID {$apolice->id}");
            $this->line("   📋 Número: {$apolice->numero_apolice}");
            $this->line("   💰 Valor segurado: {$apolice->valor_segurado}");
            $this->line("   💳 Prêmio: {$apolice->premio}");
            $this->line("   👤 Cliente: {$cliente->nome}");
            $this->sucessos++;

            // Limpar
            $apolice->delete();
        } catch (Exception $e) {
            $this->error("   ❌ Erro ao criar apólice: " . $e->getMessage());
            $this->erros++;
        }
    }

    private function testarSinistros(): void
    {
        $this->info('🚨 Testando inserção de Sinistros...');

        try {
            $apolice = Apolice::first();
            $statusSinistro = \App\Models\StatusSinistro::first();

            if (!$apolice || !$statusSinistro) {
                throw new Exception('Apólice ou status de sinistro não encontrados');
            }

            $sinistro = Sinistro::create([
                'apolice_id' => $apolice->id,
                'numero_sinistro' => 'SIN-' . uniqid(),
                'descricao' => 'Sinistro de teste automatizado',
                'valor_sinistro' => 50000.00,
                'data_ocorrencia' => now()->subDays(5)->format('Y-m-d'),
                'status_sinistro_id' => $statusSinistro->id,
            ]);

            $this->line("   ✅ Sinistro criado: ID {$sinistro->id}");
            $this->line("   📋 Número: {$sinistro->numero_sinistro}");
            $this->line("   💰 Valor: {$sinistro->valor_sinistro}");
            $this->line("   📅 Data ocorrência: {$sinistro->data_ocorrencia}");
            $this->line("   📝 Descrição: {$sinistro->descricao}");
            $this->sucessos++;

            // Limpar
            $sinistro->delete();
        } catch (Exception $e) {
            $this->error("   ❌ Erro ao criar sinistro: " . $e->getMessage());
            $this->erros++;
        }
    }

    private function testarPagamentos(): void
    {
        $this->info('💳 Testando inserção de Pagamentos...');

        try {
            $conta = Conta::first();
            $statusPagamento = \App\Models\StatusPagamento::first();

            if (!$conta || !$statusPagamento) {
                throw new Exception('Conta ou status de pagamento não encontrados');
            }

            $pagamento = Pagamento::create([
                'conta_id' => $conta->id,
                'tipo_pagamento' => 'Serviço',
                'valor' => 15000.00,
                'descricao' => 'Pagamento de teste',
                'referencia_externa' => 'PAG-' . uniqid(),
                'data_vencimento' => now()->addDays(30)->format('Y-m-d'),
                'status_pagamento_id' => $statusPagamento->id,
            ]);

            $this->line("   ✅ Pagamento criado: ID {$pagamento->id}");
            $this->line("   💰 Valor: {$pagamento->valor}");
            $this->line("   📝 Descrição: {$pagamento->descricao}");
            $this->line("   📅 Vencimento: {$pagamento->data_vencimento}");
            $this->line("   🏦 Conta: {$conta->numero_conta}");
            $this->sucessos++;

            // Limpar
            $pagamento->delete();
        } catch (Exception $e) {
            $this->error("   ❌ Erro ao criar pagamento: " . $e->getMessage());
            $this->erros++;
        }
    }

    private function testarLogsAcao(): void
    {
        $this->info('📝 Testando inserção de Logs de Ação...');

        try {
            $log = LogAcao::create([
                'acao' => 'teste_automatizado',
                'detalhes' => 'Log de teste criado automaticamente durante os testes de inserção',
            ]);

            $this->line("   ✅ Log de ação criado: ID {$log->id}");
            $this->line("   🎯 Ação: {$log->acao}");
            $this->line("   📝 Detalhes: {$log->detalhes}");
            $this->line("   🕒 Data: {$log->created_at}");
            $this->sucessos++;

            // Limpar
            $log->delete();
        } catch (Exception $e) {
            $this->error("   ❌ Erro ao criar log de ação: " . $e->getMessage());
            $this->erros++;
        }
    }

    private function gerarBI(): string
    {
        do {
            $noveDigitos = str_pad(random_int(100000000, 999999999), 9, '0', STR_PAD_LEFT);
            $duasLetras = chr(random_int(65, 90)) . chr(random_int(65, 90)); // A-Z
            $tresDigitos = str_pad(random_int(100, 999), 3, '0', STR_PAD_LEFT);
            $bi = $noveDigitos . $duasLetras . $tresDigitos;
        } while (Cliente::where('bi', $bi)->exists());

        return $bi;
    }

    private function gerarNumeroCartao(Conta $conta): string
    {
        $prefixoBanco = '4042';
        $agenciaCode = str_pad($conta->agencia->codigo_agencia, 4, '0', STR_PAD_LEFT);
        $contaId = str_pad($conta->id, 4, '0', STR_PAD_LEFT);
        $random = str_pad(random_int(1000, 9999), 4, '0', STR_PAD_LEFT);
        
        $numero = $prefixoBanco . $agenciaCode . $contaId . $random;
        
        while (Cartao::where('numero_cartao', $numero)->exists()) {
            $random = str_pad(random_int(1000, 9999), 4, '0', STR_PAD_LEFT);
            $numero = $prefixoBanco . $agenciaCode . $contaId . $random;
        }
        
        return $numero;
    }
}
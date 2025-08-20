<?php

namespace App\Services;

use App\Models\Conta;
use App\Models\Transacao;
use App\Models\OperacaoCambio;
use App\Models\TaxaCambio;
use App\Models\TipoTransacao;
use App\Models\StatusTransacao;
use App\Models\Pagamento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use InvalidArgumentException;

class TransactionService
{
    public function deposit(Conta $conta, float $valor, int $moedaId, ?string $descricao = null, ?string $referenciaExterna = null): Transacao
    {
        $this->assertPositiveAmount($valor);
        $this->assertSameCurrency($conta->moeda_id, $moedaId, 'Depósito deve utilizar a mesma moeda da conta.');

        return DB::transaction(function () use ($conta, $valor, $moedaId, $descricao, $referenciaExterna) {
            $contaLocked = Conta::whereKey($conta->id)->lockForUpdate()->firstOrFail();
            $contaLocked->saldo = round(((float)$contaLocked->saldo) + $valor, 2);
            $contaLocked->save();

            return $this->criarTransacao(
                contaOrigemId: null,
                contaDestinoId: $contaLocked->id,
                tipo: 'Depósito',
                valor: $valor,
                moedaId: $moedaId,
                descricao: $descricao,
                referenciaExterna: $referenciaExterna,
                origemExterna: true,
                destinoExterna: false
            );
        });
    }

    public function withdraw(Conta $conta, float $valor, int $moedaId, ?string $descricao = null, ?string $referenciaExterna = null): Transacao
    {
        $this->assertPositiveAmount($valor);
        $this->assertSameCurrency($conta->moeda_id, $moedaId, 'Levantamento deve utilizar a mesma moeda da conta.');

        return DB::transaction(function () use ($conta, $valor, $moedaId, $descricao, $referenciaExterna) {
            $contaLocked = Conta::whereKey($conta->id)->lockForUpdate()->firstOrFail();
            $this->assertSufficientFunds($contaLocked->saldo, $valor);
            $contaLocked->saldo = round(((float)$contaLocked->saldo) - $valor, 2);
            $contaLocked->save();

            return $this->criarTransacao(
                contaOrigemId: $contaLocked->id,
                contaDestinoId: null,
                tipo: 'Levantamento',
                valor: $valor,
                moedaId: $moedaId,
                descricao: $descricao,
                referenciaExterna: $referenciaExterna,
                origemExterna: false,
                destinoExterna: true
            );
        });
    }

    public function transferInternal(Conta $contaOrigem, Conta $contaDestino, float $valor, int $moedaId, ?string $descricao = null, ?string $referenciaExterna = null): Transacao
    {
        $this->assertPositiveAmount($valor);
        $this->assertSameCurrency($contaOrigem->moeda_id, $moedaId, 'Moeda inválida para conta de origem.');
        $this->assertSameCurrency($contaDestino->moeda_id, $moedaId, 'Moeda inválida para conta de destino.');
        if ($contaOrigem->id === $contaDestino->id) {
            throw new InvalidArgumentException('Conta de destino deve ser diferente da conta de origem.');
        }

        return DB::transaction(function () use ($contaOrigem, $contaDestino, $valor, $moedaId, $descricao, $referenciaExterna) {
            $origem = Conta::whereKey($contaOrigem->id)->lockForUpdate()->firstOrFail();
            $destino = Conta::whereKey($contaDestino->id)->lockForUpdate()->firstOrFail();

            $this->assertSufficientFunds($origem->saldo, $valor);

            $origem->saldo = round(((float)$origem->saldo) - $valor, 2);
            $destino->saldo = round(((float)$destino->saldo) + $valor, 2);

            $origem->save();
            $destino->save();

            return $this->criarTransacao(
                contaOrigemId: $origem->id,
                contaDestinoId: $destino->id,
                tipo: 'Transferência',
                valor: $valor,
                moedaId: $moedaId,
                descricao: $descricao,
                referenciaExterna: $referenciaExterna
            );
        });
    }

    public function transferExternal(?Conta $contaOrigem, ?Conta $contaDestino, float $valor, int $moedaId, array $externo, ?string $descricao = null, ?string $referenciaExterna = null): Transacao
    {
        $this->assertPositiveAmount($valor);

        return DB::transaction(function () use ($contaOrigem, $contaDestino, $valor, $moedaId, $externo, $descricao, $referenciaExterna) {
            // Idempotência por referência externa
            if (!empty($referenciaExterna)) {
                $existente = Transacao::where('referencia_externa', $referenciaExterna)->first();
                if ($existente) {
                    return $existente;
                }
            }
            $origemExterna = empty($contaOrigem);
            $destinoExterna = empty($contaDestino);

            if ($origemExterna && $destinoExterna) {
                throw new InvalidArgumentException('Pelo menos uma das pontas deve ser interna.');
            }

            if (!$origemExterna) {
                $this->assertSameCurrency($contaOrigem->moeda_id, $moedaId, 'Moeda inválida na conta de origem.');
                $contaOrigem = Conta::whereKey($contaOrigem->id)->lockForUpdate()->firstOrFail();
                $this->assertSufficientFunds($contaOrigem->saldo, $valor);
                $contaOrigem->saldo = round(((float)$contaOrigem->saldo) - $valor, 2);
                $contaOrigem->save();
            }

            if (!$destinoExterna) {
                $this->assertSameCurrency($contaDestino->moeda_id, $moedaId, 'Moeda inválida na conta de destino.');
                $contaDestino = Conta::whereKey($contaDestino->id)->lockForUpdate()->firstOrFail();
                $contaDestino->saldo = round(((float)$contaDestino->saldo) + $valor, 2);
                $contaDestino->save();
            }

            $transacao = new Transacao();
            $transacao->conta_origem_id = $contaOrigem->id ?? null;
            $transacao->conta_destino_id = $contaDestino->id ?? null;
            $transacao->origem_externa = $origemExterna;
            $transacao->destino_externa = $destinoExterna;
            $transacao->conta_externa_origem = $externo['conta_externa_origem'] ?? null;
            $transacao->banco_externo_origem = $externo['banco_externo_origem'] ?? null;
            $transacao->conta_externa_destino = $externo['conta_externa_destino'] ?? null;
            $transacao->banco_externo_destino = $externo['banco_externo_destino'] ?? null;
            $transacao->tipo_transacao_id = $this->tipoIdPorNome('Transferência');
            $transacao->valor = $valor;
            $transacao->moeda_id = $moedaId;
            $transacao->status_transacao_id = $this->statusIdPorNome('Concluída');
            $transacao->descricao = $descricao;
            $transacao->referencia_externa = $referenciaExterna;
            $transacao->save();

            return $transacao->fresh();
        });
    }

    public function exchange(?int $clienteId, Conta $contaOrigem, Conta $contaDestino, int $moedaOrigemId, int $moedaDestinoId, float $valorOrigem, ?string $descricao = null): OperacaoCambio
    {
        $this->assertPositiveAmount($valorOrigem);
        if ($contaOrigem->id === $contaDestino->id) {
            throw new InvalidArgumentException('Conta de destino deve ser diferente da conta de origem.');
        }

        return DB::transaction(function () use ($clienteId, $contaOrigem, $contaDestino, $moedaOrigemId, $moedaDestinoId, $valorOrigem, $descricao) {
            $origem = Conta::whereKey($contaOrigem->id)->lockForUpdate()->firstOrFail();
            $destino = Conta::whereKey($contaDestino->id)->lockForUpdate()->firstOrFail();

            // Buscar taxa mais recente para o par de moedas
            $taxaRecord = TaxaCambio::where('moeda_origem_id', $moedaOrigemId)
                ->where('moeda_destino_id', $moedaDestinoId)
                ->orderByDesc('data_taxa')
                ->orderByDesc('id')
                ->first();

            if (!$taxaRecord) {
                throw new InvalidArgumentException('Não há taxa de câmbio configurada para o par de moedas.');
            }

            $valorDestino = (float) bcmul((string)$valorOrigem, (string)$taxaRecord->taxa, 2);

            // Debita origem na moeda de origem
            $this->assertSameCurrency($origem->moeda_id, $moedaOrigemId, 'Moeda de origem da conta não corresponde.');
            $this->assertSufficientFunds($origem->saldo, $valorOrigem);
            $origem->saldo = round(((float)$origem->saldo) - $valorOrigem, 2);
            $origem->save();

            // Credita destino na moeda de destino
            $this->assertSameCurrency($destino->moeda_id, $moedaDestinoId, 'Moeda de destino da conta não corresponde.');
            $destino->saldo = round(((float)$destino->saldo) + $valorDestino, 2);
            $destino->save();

            // Registrar operação de câmbio
            $op = new OperacaoCambio();
            $op->cliente_id = $clienteId;
            $op->conta_origem_id = $origem->id;
            $op->conta_destino_id = $destino->id;
            $op->moeda_origem_id = $moedaOrigemId;
            $op->moeda_destino_id = $moedaDestinoId;
            $op->valor_origem = $valorOrigem;
            $op->valor_destino = $valorDestino;
            $op->taxa_utilizada = $taxaRecord->taxa;
            $op->data_operacao = Carbon::now();
            $op->save();

            // Criar lançamentos em transacoes para refletir no extrato
            // 1) Débito na conta de origem na moeda de origem
            $debito = new Transacao();
            $debito->conta_origem_id = $origem->id;
            $debito->conta_destino_id = null;
            $debito->origem_externa = false;
            $debito->destino_externa = true;
            $debito->tipo_transacao_id = $this->tipoIdPorNome('Câmbio - Débito');
            $debito->valor = $valorOrigem;
            $debito->moeda_id = $moedaOrigemId;
            $debito->status_transacao_id = $this->statusIdPorNome('Concluída');
            $debito->descricao = $descricao ?: 'Débito por operação de câmbio';
            $debito->referencia_externa = 'CAMBIO:' . $op->id . ':DEB';
            $debito->save();

            // 2) Crédito na conta de destino na moeda de destino
            $credito = new Transacao();
            $credito->conta_origem_id = null;
            $credito->conta_destino_id = $destino->id;
            $credito->origem_externa = true;
            $credito->destino_externa = false;
            $credito->tipo_transacao_id = $this->tipoIdPorNome('Câmbio - Crédito');
            $credito->valor = $valorDestino;
            $credito->moeda_id = $moedaDestinoId;
            $credito->status_transacao_id = $this->statusIdPorNome('Concluída');
            $credito->descricao = $descricao ?: 'Crédito por operação de câmbio';
            $credito->referencia_externa = 'CAMBIO:' . $op->id . ':CRED';
            $credito->save();

            return $op->fresh();
        });
    }

    public function pay(Conta $conta, string $parceiro, string $referencia, float $valor, int $moedaId, ?string $descricao = null): Pagamento
    {
        $this->assertPositiveAmount($valor);
        $this->assertSameCurrency($conta->moeda_id, $moedaId, 'Pagamento deve utilizar a mesma moeda da conta.');

        return DB::transaction(function () use ($conta, $parceiro, $referencia, $valor, $moedaId, $descricao) {
            // Idempotência por referência do parceiro
            $refExterna = 'PAY:' . $referencia;
            $transacaoExistente = Transacao::where('referencia_externa', $refExterna)->first();
            if ($transacaoExistente) {
                $pagamentoExistente = Pagamento::where('conta_id', $conta->id)->where('referencia', $referencia)->first();
                if ($pagamentoExistente) {
                    return $pagamentoExistente;
                }
            }
            $contaLocked = Conta::whereKey($conta->id)->lockForUpdate()->firstOrFail();
            $this->assertSufficientFunds($contaLocked->saldo, $valor);
            $contaLocked->saldo = round(((float)$contaLocked->saldo) - $valor, 2);
            $contaLocked->save();

            $pagamento = new Pagamento();
            $pagamento->conta_id = $contaLocked->id;
            $pagamento->parceiro = $parceiro;
            $pagamento->referencia = $referencia;
            $pagamento->valor = $valor;
            $pagamento->moeda_id = $moedaId;
            // status_pagamento: buscar/ criar "Concluído"
            $statusPagamentoId = \App\Models\StatusPagamento::firstOrCreate(
                ['nome' => 'Concluído'],
                ['descricao' => 'Pagamento concluído']
            )->id;
            $pagamento->status_pagamento_id = (int) $statusPagamentoId;
            $pagamento->data_pagamento = Carbon::now();
            $pagamento->save();

            // Registrar transação para refletir no extrato
            $transacao = new Transacao();
            $transacao->conta_origem_id = $contaLocked->id;
            $transacao->conta_destino_id = null;
            $transacao->origem_externa = false;
            $transacao->destino_externa = true;
            $transacao->conta_externa_destino = $parceiro;
            $transacao->banco_externo_destino = null;
            $transacao->tipo_transacao_id = $this->tipoIdPorNome('Pagamento');
            $transacao->valor = $valor;
            $transacao->moeda_id = $moedaId;
            $transacao->status_transacao_id = $this->statusIdPorNome('Concluída');
            $transacao->descricao = $descricao ?: ('Pagamento para ' . $parceiro);
            $transacao->referencia_externa = $refExterna;
            $transacao->save();

            return $pagamento->fresh();
        });
    }

    private function criarTransacao(?int $contaOrigemId, ?int $contaDestinoId, string $tipo, float $valor, int $moedaId, ?string $descricao = null, ?string $referenciaExterna = null, bool $origemExterna = false, bool $destinoExterna = false): Transacao
    {
        if (!empty($referenciaExterna)) {
            $existente = Transacao::where('referencia_externa', $referenciaExterna)->first();
            if ($existente) {
                return $existente;
            }
        }
        $transacao = new Transacao();
        $transacao->conta_origem_id = $contaOrigemId;
        $transacao->conta_destino_id = $contaDestinoId;
        $transacao->origem_externa = $origemExterna;
        $transacao->destino_externa = $destinoExterna;
        $transacao->tipo_transacao_id = $this->tipoIdPorNome($tipo);
        $transacao->valor = $valor;
        $transacao->moeda_id = $moedaId;
        $transacao->status_transacao_id = $this->statusIdPorNome('Concluída');
        $transacao->descricao = $descricao;
        $transacao->referencia_externa = $referenciaExterna;
        $transacao->save();
        return $transacao->fresh();
    }

    private function assertPositiveAmount(float $valor): void
    {
        if ($valor <= 0) {
            throw new InvalidArgumentException('O valor deve ser maior que zero.');
        }
    }

    private function assertSufficientFunds($saldoAtual, float $valor): void
    {
        if ((float)$saldoAtual < $valor) {
            throw new InvalidArgumentException('Saldo insuficiente.');
        }
    }

    private function assertSameCurrency(int $contaMoedaId, int $moedaId, string $message): void
    {
        if ($contaMoedaId !== $moedaId) {
            throw new InvalidArgumentException($message);
        }
    }

    private function tipoIdPorNome(string $nome): int
    {
        $tipo = TipoTransacao::where('nome', $nome)->first();
        if (!$tipo) {
            // Tentar criar o tipo se não existir
            try {
                $tipo = TipoTransacao::create([
                    'nome' => $nome,
                    'descricao' => 'Tipo de transação criado automaticamente'
                ]);
            } catch (\Exception $e) {
                throw new InvalidArgumentException("Tipo de transação '{$nome}' não encontrado e não foi possível criar automaticamente.");
            }
        }
        return (int) $tipo->id;
    }

    private function statusIdPorNome(string $nome): int
    {
        $status = StatusTransacao::where('nome', $nome)->first();
        if (!$status) {
            // Tentar criar o status se não existir
            try {
                $status = StatusTransacao::create([
                    'nome' => $nome,
                    'descricao' => 'Status de transação criado automaticamente'
                ]);
            } catch (\Exception $e) {
                throw new InvalidArgumentException("Status de transação '{$nome}' não encontrado e não foi possível criar automaticamente.");
            }
        }
        return (int) $status->id;
    }
}


<?php

use App\Models\TipoConta;
use App\Models\TipoCliente;
use App\Models\TipoCartao;
use App\Models\TipoTransacao;
use App\Models\TipoSeguro;
use App\Models\StatusConta;
use App\Models\StatusTransacao;
use App\Models\StatusCliente;
use App\Models\StatusCartao;
use App\Models\StatusApolice;
use App\Models\StatusSinistro;
use App\Models\TaxaCambio;
use App\Models\Moeda;

return [
    // key => [model, title, display_field]
    'tipo-conta' => ['model' => TipoConta::class, 'title' => 'Tipo de Conta', 'field' => 'nome'],
    'tipo-cliente' => ['model' => TipoCliente::class, 'title' => 'Tipo de Cliente', 'field' => 'nome'],
    'tipo-cartao' => ['model' => TipoCartao::class, 'title' => 'Tipo de Cartão', 'field' => 'nome'],
    'tipo-transacao' => ['model' => TipoTransacao::class, 'title' => 'Tipo de Transação', 'field' => 'nome'],
    'tipo-seguro' => ['model' => TipoSeguro::class, 'title' => 'Tipo de Seguro', 'field' => 'nome'],

    'status-conta' => ['model' => StatusConta::class, 'title' => 'Status de Conta', 'field' => 'nome'],
    'status-transacao' => ['model' => StatusTransacao::class, 'title' => 'Status de Transação', 'field' => 'nome'],
    'status-cliente' => ['model' => StatusCliente::class, 'title' => 'Status de Cliente', 'field' => 'nome'],
    'status-cartao' => ['model' => StatusCartao::class, 'title' => 'Status de Cartão', 'field' => 'nome'],
    'status-apolice' => ['model' => StatusApolice::class, 'title' => 'Status de Apólice', 'field' => 'nome'],
    'status-sinistro' => ['model' => StatusSinistro::class, 'title' => 'Status de Sinistro', 'field' => 'nome'],

    'taxa-cambio' => ['model' => TaxaCambio::class, 'title' => 'Taxas de Câmbio', 'field' => 'descricao'],
    'moedas' => ['model' => Moeda::class, 'title' => 'Moedas', 'field' => 'nome'],
];

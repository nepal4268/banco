<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Moeda extends Model
{
    use HasFactory;

    protected $table = 'moedas';
    public $timestamps = false;

    protected $fillable = [
        'codigo',
        'nome',
        'simbolo',
    ];

    // Relacionamentos
    public function contas(): HasMany
    {
        return $this->hasMany(Conta::class, 'moeda_id');
    }

    public function transacoes(): HasMany
    {
        return $this->hasMany(Transacao::class, 'moeda_id');
    }

    public function pagamentos(): HasMany
    {
        return $this->hasMany(Pagamento::class, 'moeda_id');
    }

    public function taxasCambioOrigem(): HasMany
    {
        return $this->hasMany(TaxaCambio::class, 'moeda_origem_id');
    }

    public function taxasCambioDestino(): HasMany
    {
        return $this->hasMany(TaxaCambio::class, 'moeda_destino_id');
    }

    public function operacoesCambioOrigem(): HasMany
    {
        return $this->hasMany(OperacaoCambio::class, 'moeda_origem_id');
    }

    public function operacoesCambioDestino(): HasMany
    {
        return $this->hasMany(OperacaoCambio::class, 'moeda_destino_id');
    }
}

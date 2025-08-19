<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OperacaoCambio extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'operacoes_cambio';

    protected $fillable = [
        'cliente_id',
        'conta_origem_id',
        'conta_destino_id',
        'moeda_origem_id',
        'moeda_destino_id',
        'valor_origem',
        'valor_destino',
        'taxa_utilizada',
        'data_operacao',
    ];

    protected $casts = [
        'valor_origem' => 'decimal:2',
        'valor_destino' => 'decimal:2',
        'taxa_utilizada' => 'decimal:8',
        'data_operacao' => 'datetime',
    ];

    // Relacionamentos
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function contaOrigem(): BelongsTo
    {
        return $this->belongsTo(Conta::class, 'conta_origem_id');
    }

    public function contaDestino(): BelongsTo
    {
        return $this->belongsTo(Conta::class, 'conta_destino_id');
    }

    public function moedaOrigem(): BelongsTo
    {
        return $this->belongsTo(Moeda::class, 'moeda_origem_id');
    }

    public function moedaDestino(): BelongsTo
    {
        return $this->belongsTo(Moeda::class, 'moeda_destino_id');
    }
}

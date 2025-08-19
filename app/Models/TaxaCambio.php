<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxaCambio extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'taxas_cambio';

    protected $fillable = [
        'moeda_origem_id',
        'moeda_destino_id',
        'taxa_compra',
        'taxa_venda',
        'ativa',
        'data_taxa',
    ];

    protected $casts = [
        'taxa_compra' => 'decimal:8',
        'taxa_venda' => 'decimal:8',
        'ativa' => 'boolean',
        'data_taxa' => 'date',
    ];

    // Relacionamentos
    public function moedaOrigem(): BelongsTo
    {
        return $this->belongsTo(Moeda::class, 'moeda_origem_id');
    }

    public function moedaDestino(): BelongsTo
    {
        return $this->belongsTo(Moeda::class, 'moeda_destino_id');
    }

    public function operacoesCambio(): HasMany
    {
        return $this->hasMany(OperacaoCambio::class, 'taxa_cambio_id');
    }
}

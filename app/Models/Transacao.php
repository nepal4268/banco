<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transacao extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transacoes';

    protected $fillable = [
        'conta_origem_id',
        'origem_externa',
        'conta_externa_origem',
        'banco_externo_origem',
        'conta_destino_id',
        'destino_externa',
        'conta_externa_destino',
        'banco_externo_destino',
        'tipo_transacao_id',
        'valor',
        'moeda_id',
        'status_transacao_id',
        'descricao',
        'referencia_externa',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'origem_externa' => 'boolean',
        'destino_externa' => 'boolean',
    ];

    // Relacionamentos
    public function contaOrigem(): BelongsTo
    {
        return $this->belongsTo(Conta::class, 'conta_origem_id');
    }

    public function contaDestino(): BelongsTo
    {
        return $this->belongsTo(Conta::class, 'conta_destino_id');
    }

    public function tipoTransacao(): BelongsTo
    {
        return $this->belongsTo(TipoTransacao::class, 'tipo_transacao_id');
    }

    public function moeda(): BelongsTo
    {
        return $this->belongsTo(Moeda::class, 'moeda_id');
    }

    public function statusTransacao(): BelongsTo
    {
        return $this->belongsTo(StatusTransacao::class, 'status_transacao_id');
    }
}

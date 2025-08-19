<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'clientes';

    protected $fillable = [
        'nome',
        'sexo',
        'bi',
        'tipo_cliente_id',
        'status_cliente_id',
    ];

    protected $casts = [
        'sexo' => 'string',
    ];

    // Relacionamentos
    public function tipoCliente(): BelongsTo
    {
        return $this->belongsTo(TipoCliente::class, 'tipo_cliente_id');
    }

    public function statusCliente(): BelongsTo
    {
        return $this->belongsTo(StatusCliente::class, 'status_cliente_id');
    }

    public function contas(): HasMany
    {
        return $this->hasMany(Conta::class, 'cliente_id');
    }

    public function operacoesCambio(): HasMany
    {
        return $this->hasMany(OperacaoCambio::class, 'cliente_id');
    }

    public function apolices(): HasMany
    {
        return $this->hasMany(Apolice::class, 'cliente_id');
    }
}

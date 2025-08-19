<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sinistro extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sinistros';

    protected $fillable = [
        'apolice_id',
        'descricao',
        'valor_reivindicado',
        'valor_pago',
        'data_sinistro',
        'status_sinistro_id',
    ];

    protected $casts = [
        'valor_reivindicado' => 'decimal:2',
        'valor_pago' => 'decimal:2',
        'data_sinistro' => 'date',
    ];

    // Relacionamentos
    public function apolice(): BelongsTo
    {
        return $this->belongsTo(Apolice::class);
    }

    public function statusSinistro(): BelongsTo
    {
        return $this->belongsTo(StatusSinistro::class, 'status_sinistro_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Apolice extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'apolices';

    protected $fillable = [
        'cliente_id',
        'tipo_seguro_id',
        'numero_apolice',
        'inicio_vigencia',
        'fim_vigencia',
        'status_apolice_id',
        'premio_mensal',
    ];

    protected $casts = [
        'inicio_vigencia' => 'date',
        'fim_vigencia' => 'date',
        'premio_mensal' => 'decimal:2',
    ];

    // Relacionamentos
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function tipoSeguro(): BelongsTo
    {
        return $this->belongsTo(TipoSeguro::class, 'tipo_seguro_id');
    }

    public function statusApolice(): BelongsTo
    {
        return $this->belongsTo(StatusApolice::class, 'status_apolice_id');
    }

    public function sinistros(): HasMany
    {
        return $this->hasMany(Sinistro::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cartao extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cartoes';

    protected $fillable = [
        'conta_id',
        'tipo_cartao_id',
        'numero_cartao',
        'validade',
        'limite',
        'status_cartao_id',
        'numero_cartao_hash',
    ];

    protected $casts = [
        'validade' => 'date',
        'numero_cartao' => 'encrypted',
        'limite' => 'decimal:2',
        'numero_cartao_hash' => 'string',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function (Cartao $cartao) {
            if ($cartao->isDirty('numero_cartao')) {
                // Usar hash estável para unicidade; SHA-256 para 64 hex chars
                $numeroPlano = $cartao->numero_cartao;
                $cartao->numero_cartao_hash = hash('sha256', $numeroPlano);
            }
        });
    }

    // Relacionamentos
    public function conta(): BelongsTo
    {
        return $this->belongsTo(Conta::class);
    }

    public function tipoCartao(): BelongsTo
    {
        return $this->belongsTo(TipoCartao::class, 'tipo_cartao_id');
    }

    public function statusCartao(): BelongsTo
    {
        return $this->belongsTo(StatusCartao::class, 'status_cartao_id');
    }
}

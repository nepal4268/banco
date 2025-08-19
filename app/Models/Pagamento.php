<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pagamento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pagamentos';

    protected $fillable = [
        'conta_id',
        'parceiro',
        'referencia',
        'valor',
        'moeda_id',
        'data_pagamento',
        'status_pagamento_id',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'data_pagamento' => 'datetime',
    ];

    // Relacionamentos
    public function conta(): BelongsTo
    {
        return $this->belongsTo(Conta::class);
    }

    public function moeda(): BelongsTo
    {
        return $this->belongsTo(Moeda::class);
    }

    public function statusPagamento(): BelongsTo
    {
        return $this->belongsTo(StatusPagamento::class, 'status_pagamento_id');
    }
}

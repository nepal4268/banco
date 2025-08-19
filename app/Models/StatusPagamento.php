<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatusPagamento extends Model
{
    use HasFactory;

    protected $table = 'status_pagamento';
    public $timestamps = false;

    protected $fillable = [
        'nome',
        'descricao',
    ];

    // Relacionamentos
    public function pagamentos(): HasMany
    {
        return $this->hasMany(Pagamento::class, 'status_pagamento_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatusCartao extends Model
{
    use HasFactory;

    protected $table = 'status_cartao';
    public $timestamps = false;

    protected $fillable = [
        'nome',
        'descricao',
    ];

    // Relacionamentos
    public function cartoes(): HasMany
    {
        return $this->hasMany(Cartao::class, 'status_cartao_id');
    }
}

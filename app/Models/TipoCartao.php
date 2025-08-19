<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoCartao extends Model
{
    use HasFactory;

    protected $table = 'tipos_cartao';
    public $timestamps = false;

    protected $fillable = [
        'nome',
        'descricao',
    ];

    // Relacionamentos
    public function cartoes(): HasMany
    {
        return $this->hasMany(Cartao::class, 'tipo_cartao_id');
    }
}

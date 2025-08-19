<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoCliente extends Model
{
    use HasFactory;

    protected $table = 'tipos_cliente';
    public $timestamps = false;

    protected $fillable = [
        'nome',
        'descricao',
    ];

    // Relacionamentos
    public function clientes(): HasMany
    {
        return $this->hasMany(Cliente::class, 'tipo_cliente_id');
    }
}

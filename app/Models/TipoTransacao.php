<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoTransacao extends Model
{
    use HasFactory;

    protected $table = 'tipos_transacao';
    public $timestamps = false;

    protected $fillable = ['nome', 'descricao'];

    public function transacoes(): HasMany
    {
        return $this->hasMany(Transacao::class, 'tipo_transacao_id');
    }
}

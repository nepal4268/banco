<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatusTransacao extends Model
{
    use HasFactory;

    protected $table = 'status_transacao';
    public $timestamps = false;

    protected $fillable = ['nome', 'descricao'];

    public function transacoes(): HasMany
    {
        return $this->hasMany(Transacao::class, 'status_transacao_id');
    }
}

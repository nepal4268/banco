<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoConta extends Model
{
    use HasFactory;

    protected $table = 'tipos_conta';
    public $timestamps = false;

    protected $fillable = ['nome', 'descricao'];

    public function contas(): HasMany
    {
        return $this->hasMany(Conta::class, 'tipo_conta_id');
    }
}

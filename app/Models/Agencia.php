<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agencia extends Model
{
    use HasFactory;

    protected $table = 'agencias';
    public $timestamps = false;

    protected $fillable = [
        'codigo_banco',
        'codigo_agencia',
        'nome',
        'endereco',
        'telefone',
        'email',
        'ativa',
    ];

    protected $casts = [
        'telefone' => 'array',
        'ativa' => 'boolean',
    ];

    // Relacionamentos
    public function contas(): HasMany
    {
        return $this->hasMany(Conta::class);
    }

    // Métodos auxiliares
    public function getCodigoCompleto(): string
    {
        return $this->codigo_banco . $this->codigo_agencia;
    }

    public static function getCodigoBancoPadrao(): string
    {
        return '0042';
    }

    // Scope para agências ativas
    public function scopeAtivas($query)
    {
        return $query->where('ativa', true);
    }
}
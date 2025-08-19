<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoSeguro extends Model
{
    use HasFactory;

    protected $table = 'tipos_seguro';
    public $timestamps = false;

    protected $fillable = [
        'nome',
        'descricao',
        'cobertura',
        'premio_mensal',
    ];

    protected $casts = [
        'cobertura' => 'decimal:2',
        'premio_mensal' => 'decimal:2',
    ];

    // Relacionamentos
    public function apolices(): HasMany
    {
        return $this->hasMany(Apolice::class, 'tipo_seguro_id');
    }
}

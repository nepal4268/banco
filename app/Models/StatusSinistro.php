<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatusSinistro extends Model
{
    use HasFactory;

    protected $table = 'status_sinistro';
    public $timestamps = false;

    protected $fillable = [
        'nome',
        'descricao',
    ];

    // Relacionamentos
    public function sinistros(): HasMany
    {
        return $this->hasMany(Sinistro::class, 'status_sinistro_id');
    }
}

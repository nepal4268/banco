<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatusApolice extends Model
{
    use HasFactory;

    protected $table = 'status_apolice';
    public $timestamps = false;

    protected $fillable = [
        'nome',
        'descricao',
    ];

    // Relacionamentos
    public function apolices(): HasMany
    {
        return $this->hasMany(Apolice::class, 'status_apolice_id');
    }
}

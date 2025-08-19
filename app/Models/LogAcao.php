<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogAcao extends Model
{
    use HasFactory;

    protected $table = 'log_acoes';
    public $timestamps = false; // Usa apenas created_at

    protected $fillable = [
        'usuario_id',
        'acao',
        'detalhes',
        'ip_origem',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Relacionamentos
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }
}

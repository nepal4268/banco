<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
	use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

	protected $table = 'usuarios';

	protected $fillable = [
		'nome',
		'email',
		'bi',
		'sexo',
		'data_nascimento',
		'telefone',
		'senha',
		'perfil_id',
		'agencia_id',
		'status_usuario',
		'endereco',
		'cidade',
		'provincia',
	];

	protected $hidden = [
		'senha',
	];

	protected $casts = [
		'senha' => 'hashed',
		'telefone' => 'array',
		'data_nascimento' => 'date',
	];

        // Override password field for Laravel authentication

	public function getAuthPassword()
	{
		return $this->senha;
	}
    // Relacionamentos

	public function perfil(): BelongsTo
	{
		return $this->belongsTo(Perfil::class, 'perfil_id');
	}

	public function agencia(): BelongsTo
	{
		return $this->belongsTo(Agencia::class, 'agencia_id');
	}

	public function permissoes(): BelongsToMany
	{
		return $this->belongsToMany(Permissao::class, 'usuario_permissao', 'usuario_id', 'permissao_id')
					->withTimestamps()
					->withPivot('deleted_at')
					->wherePivot('deleted_at', null);
	}

	public function logAcoes(): HasMany
	{
		return $this->hasMany(LogAcao::class, 'usuario_id');
	}
// Verificar se usuário tem permissão
	public function hasPermission(string $permissionCode): bool
	{
        // Verifica permissões diretas do usuário
		if ($this->permissoes()->where('code', $permissionCode)->exists()) {
		 return true;
		}
         // Verifica permissões do perfil
		if ($this->perfil && $this->perfil->permissoes()->where('code', $permissionCode)->exists()) {
		 return true;
		}
		return false;
	}
}
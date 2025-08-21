<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'nome',
        'email',
        'senha',
        'bi',
        'sexo',
        'data_nascimento',
        'telefone',
        'perfil_id',
        'agencia_id',
        'ativo',
        'endereco',
        'cidade',
        'provincia',
    ];

    protected $hidden = [
        'senha',
        'remember_token',
    ];

    protected $casts = [
        'senha' => 'hashed',
        'data_nascimento' => 'date',
        'ativo' => 'boolean',
    ];

    // Override password field for Laravel authentication
    public function getAuthPassword()
    {
        return $this->senha;
    }

    // Relacionamentos
    public function perfil()
    {
        return $this->belongsTo(Perfil::class, 'perfil_id');
    }

    public function agencia()
    {
        return $this->belongsTo(Agencia::class, 'agencia_id');
    }

    public function permissoes()
    {
        return $this->belongsToMany(Permissao::class, 'usuario_permissao', 'usuario_id', 'permissao_id')
                    ->withTimestamps()
                    ->withPivot('deleted_at')
                    ->wherePivot('deleted_at', null);
    }

    public function logAcoes()
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

    // Verificar se usuário tem qualquer uma das permissões
    public function hasAnyPermission(array $permissionCodes): bool
    {
        foreach ($permissionCodes as $code) {
            if ($this->hasPermission($code)) {
                return true;
            }
        }
        return false;
    }

    // Verificar se usuário tem todas as permissões
    public function hasAllPermissions(array $permissionCodes): bool
    {
        foreach ($permissionCodes as $code) {
            if (!$this->hasPermission($code)) {
                return false;
            }
        }
        return true;
    }

    // Verificar se usuário é administrador
    public function isAdmin(): bool
    {
        return $this->hasPermission('admin.all') || 
               ($this->perfil && $this->perfil->nivel >= 3);
    }

    // Verificar se usuário é gerente
    public function isManager(): bool
    {
        return $this->hasPermission('admin.manage') || 
               ($this->perfil && $this->perfil->nivel >= 2);
    }

    // Obter nome para exibição
    public function getDisplayNameAttribute()
    {
        return $this->nome;
    }

    // Obter avatar inicial
    public function getAvatarInitialAttribute()
    {
        return strtoupper(substr($this->nome, 0, 1));
    }
}

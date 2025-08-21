<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Perfil;
use App\Models\Permissao;

class PerfilSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $perfis = [
            [
                'nome' => 'Administrador',
                'descricao' => 'Acesso total ao sistema',
                'nivel' => 3,
                'permissoes' => [
                    'admin.all',
                    'clientes.view', 'clientes.create', 'clientes.edit', 'clientes.delete',
                    'contas.view', 'contas.create', 'contas.edit', 'contas.delete',
                    'cartoes.view', 'cartoes.create', 'cartoes.edit', 'cartoes.delete',
                    'transacoes.view', 'transacoes.create', 'transacoes.edit', 'transacoes.delete',
                    'seguros.view', 'seguros.create', 'seguros.edit', 'seguros.delete',
                    'relatorios.view', 'relatorios.export',
                    'admin.view', 'admin.usuarios', 'admin.agencias', 'admin.perfis', 
                    'admin.permissoes', 'admin.tabelas', 'admin.config', 'admin.auditoria'
                ]
            ],
            [
                'nome' => 'Gerente',
                'descricao' => 'Gerente de agência com acesso amplo',
                'nivel' => 2,
                'permissoes' => [
                    'clientes.view', 'clientes.create', 'clientes.edit',
                    'contas.view', 'contas.create', 'contas.edit',
                    'cartoes.view', 'cartoes.create', 'cartoes.edit',
                    'transacoes.view', 'transacoes.create',
                    'seguros.view', 'seguros.create', 'seguros.edit',
                    'relatorios.view', 'relatorios.export',
                    'admin.view', 'admin.usuarios', 'admin.agencias'
                ]
            ],
            [
                'nome' => 'Atendente',
                'descricao' => 'Atendente de agência com acesso básico',
                'nivel' => 1,
                'permissoes' => [
                    'clientes.view', 'clientes.create',
                    'contas.view', 'contas.create',
                    'cartoes.view', 'cartoes.create',
                    'transacoes.view',
                    'seguros.view',
                    'relatorios.view'
                ]
            ],
            [
                'nome' => 'Consultor',
                'descricao' => 'Consultor com acesso apenas de visualização',
                'nivel' => 1,
                'permissoes' => [
                    'clientes.view',
                    'contas.view',
                    'cartoes.view',
                    'transacoes.view',
                    'seguros.view',
                    'relatorios.view'
                ]
            ]
        ];

        foreach ($perfis as $perfilData) {
            $permissoes = $perfilData['permissoes'];
            unset($perfilData['permissoes']);
            
            $perfil = Perfil::updateOrCreate(
                ['nome' => $perfilData['nome']],
                $perfilData
            );

            // Associar permissões ao perfil
            $permissoesIds = Permissao::whereIn('code', $permissoes)->pluck('id');
            $perfil->permissoes()->sync($permissoesIds);
        }
    }
}
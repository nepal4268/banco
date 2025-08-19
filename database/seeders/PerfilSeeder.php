<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        // Verificar se os perfis já existem (criados na migration)
        if (Perfil::count() > 0) {
            $this->associarPermissoes();
            return;
        }

        // Criar perfis se não existirem
        $perfis = [
            [
                'nome' => 'Administrador',
                'descricao' => 'Acesso completo ao sistema, incluindo configurações e gestão de usuários',
                'ativo' => true
            ],
            [
                'nome' => 'Gerente',
                'descricao' => 'Acesso a operações bancárias, relatórios e gestão de clientes',
                'ativo' => true
            ],
            [
                'nome' => 'Atendente',
                'descricao' => 'Acesso a operações básicas de atendimento ao cliente',
                'ativo' => true
            ],
            [
                'nome' => 'Auditor',
                'descricao' => 'Acesso apenas a relatórios e logs de auditoria',
                'ativo' => true
            ]
        ];

        foreach ($perfis as $perfilData) {
            Perfil::create($perfilData);
        }

        $this->command->info('✅ Perfis criados com sucesso!');
        
        $this->associarPermissoes();
    }

    private function associarPermissoes(): void
    {
        $perfilAdmin = Perfil::where('nome', 'Administrador')->first();
        $perfilGerente = Perfil::where('nome', 'Gerente')->first();
        $perfilAtendente = Perfil::where('nome', 'Atendente')->first();
        $perfilAuditor = Perfil::where('nome', 'Auditor')->first();

        // Administrador: Todas as permissões
        if ($perfilAdmin) {
            $todasPermissoes = Permissao::all()->pluck('id')->toArray();
            $perfilAdmin->permissoes()->sync($todasPermissoes);
        }

        // Gerente: Operações bancárias e relatórios (exceto configurações de sistema)
        if ($perfilGerente) {
            $permissoesGerente = Permissao::whereIn('code', [
                'clientes.view', 'clientes.create', 'clientes.edit',
                'contas.view', 'contas.create', 'contas.edit', 'contas.depositar', 'contas.levantar',
                'transacoes.view', 'transacoes.create', 'transacoes.transferir', 'transacoes.transferir_externo', 'transacoes.cambio',
                'cartoes.view', 'cartoes.create', 'cartoes.edit', 'cartoes.bloquear',
                'seguros.view', 'seguros.create', 'seguros.edit',
                'cambio.view', 'cambio.edit',
                'relatorios.dashboard', 'relatorios.transacoes', 'relatorios.extratos', 'relatorios.auditoria',
                'usuarios.view'
            ])->pluck('id')->toArray();
            
            $perfilGerente->permissoes()->sync($permissoesGerente);
        }

        // Atendente: Operações básicas de atendimento
        if ($perfilAtendente) {
            $permissoesAtendente = Permissao::whereIn('code', [
                'clientes.view', 'clientes.create', 'clientes.edit',
                'contas.view', 'contas.create', 'contas.depositar', 'contas.levantar',
                'transacoes.view', 'transacoes.create', 'transacoes.transferir',
                'cartoes.view', 'cartoes.create', 'cartoes.bloquear',
                'seguros.view', 'seguros.create',
                'cambio.view',
                'relatorios.dashboard', 'relatorios.extratos'
            ])->pluck('id')->toArray();
            
            $perfilAtendente->permissoes()->sync($permissoesAtendente);
        }

        // Auditor: Apenas visualização de relatórios e auditoria
        if ($perfilAuditor) {
            $permissoesAuditor = Permissao::whereIn('code', [
                'clientes.view',
                'contas.view',
                'transacoes.view',
                'cartoes.view',
                'seguros.view',
                'cambio.view',
                'relatorios.dashboard', 'relatorios.transacoes', 'relatorios.extratos', 'relatorios.auditoria',
                'usuarios.view'
            ])->pluck('id')->toArray();
            
            $perfilAuditor->permissoes()->sync($permissoesAuditor);
        }

        $this->command->info('✅ Permissões associadas aos perfis:');
        $this->command->info('   👑 Administrador: ' . ($perfilAdmin ? $perfilAdmin->permissoes->count() : 0) . ' permissões (todas)');
        $this->command->info('   👔 Gerente: ' . ($perfilGerente ? $perfilGerente->permissoes->count() : 0) . ' permissões');
        $this->command->info('   👤 Atendente: ' . ($perfilAtendente ? $perfilAtendente->permissoes->count() : 0) . ' permissões');
        $this->command->info('   🔍 Auditor: ' . ($perfilAuditor ? $perfilAuditor->permissoes->count() : 0) . ' permissões');
    }
}
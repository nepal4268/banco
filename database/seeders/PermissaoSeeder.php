<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permissao;

class PermissaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissoes = [
            // Clientes
            ['nome' => 'Visualizar Clientes', 'code' => 'clientes.view', 'descricao' => 'Permite visualizar lista de clientes'],
            ['nome' => 'Criar Clientes', 'code' => 'clientes.create', 'descricao' => 'Permite criar novos clientes'],
            ['nome' => 'Editar Clientes', 'code' => 'clientes.edit', 'descricao' => 'Permite editar clientes existentes'],
            ['nome' => 'Excluir Clientes', 'code' => 'clientes.delete', 'descricao' => 'Permite excluir clientes'],
            
            // Contas
            ['nome' => 'Visualizar Contas', 'code' => 'contas.view', 'descricao' => 'Permite visualizar lista de contas'],
            ['nome' => 'Criar Contas', 'code' => 'contas.create', 'descricao' => 'Permite criar novas contas'],
            ['nome' => 'Editar Contas', 'code' => 'contas.edit', 'descricao' => 'Permite editar contas existentes'],
            ['nome' => 'Excluir Contas', 'code' => 'contas.delete', 'descricao' => 'Permite excluir contas'],
            
            // Cartões
            ['nome' => 'Visualizar Cartões', 'code' => 'cartoes.view', 'descricao' => 'Permite visualizar lista de cartões'],
            ['nome' => 'Criar Cartões', 'code' => 'cartoes.create', 'descricao' => 'Permite criar novos cartões'],
            ['nome' => 'Editar Cartões', 'code' => 'cartoes.edit', 'descricao' => 'Permite editar cartões existentes'],
            ['nome' => 'Excluir Cartões', 'code' => 'cartoes.delete', 'descricao' => 'Permite excluir cartões'],
            
            // Transações
            ['nome' => 'Visualizar Transações', 'code' => 'transacoes.view', 'descricao' => 'Permite visualizar lista de transações'],
            ['nome' => 'Criar Transações', 'code' => 'transacoes.create', 'descricao' => 'Permite criar novas transações'],
            ['nome' => 'Editar Transações', 'code' => 'transacoes.edit', 'descricao' => 'Permite editar transações existentes'],
            ['nome' => 'Excluir Transações', 'code' => 'transacoes.delete', 'descricao' => 'Permite excluir transações'],
            
            // Seguros
            ['nome' => 'Visualizar Seguros', 'code' => 'seguros.view', 'descricao' => 'Permite visualizar lista de seguros'],
            ['nome' => 'Criar Seguros', 'code' => 'seguros.create', 'descricao' => 'Permite criar novos seguros'],
            ['nome' => 'Editar Seguros', 'code' => 'seguros.edit', 'descricao' => 'Permite editar seguros existentes'],
            ['nome' => 'Excluir Seguros', 'code' => 'seguros.delete', 'descricao' => 'Permite excluir seguros'],
            
            // Relatórios
            ['nome' => 'Visualizar Relatórios', 'code' => 'relatorios.view', 'descricao' => 'Permite visualizar relatórios'],
            ['nome' => 'Exportar Relatórios', 'code' => 'relatorios.export', 'descricao' => 'Permite exportar relatórios'],
            
            // Administração
            ['nome' => 'Acesso Administrativo', 'code' => 'admin.view', 'descricao' => 'Permite acessar área administrativa'],
            ['nome' => 'Gerenciar Usuários', 'code' => 'admin.usuarios', 'descricao' => 'Permite gerenciar usuários do sistema'],
            ['nome' => 'Gerenciar Agências', 'code' => 'admin.agencias', 'descricao' => 'Permite gerenciar agências'],
            ['nome' => 'Gerenciar Perfis', 'code' => 'admin.perfis', 'descricao' => 'Permite gerenciar perfis de usuário'],
            ['nome' => 'Gerenciar Permissões', 'code' => 'admin.permissoes', 'descricao' => 'Permite gerenciar permissões do sistema'],
            ['nome' => 'Gerenciar Tabelas', 'code' => 'admin.tabelas', 'descricao' => 'Permite gerenciar tabelas administrativas'],
            ['nome' => 'Configurações do Sistema', 'code' => 'admin.config', 'descricao' => 'Permite alterar configurações do sistema'],
            ['nome' => 'Logs de Auditoria', 'code' => 'admin.auditoria', 'descricao' => 'Permite visualizar logs de auditoria'],
            ['nome' => 'Administrador Total', 'code' => 'admin.all', 'descricao' => 'Acesso total ao sistema'],
        ];

        foreach ($permissoes as $permissao) {
            Permissao::updateOrCreate(
                ['code' => $permissao['code']],
                [
                    'label' => $permissao['nome'],
                    'descricao' => $permissao['descricao']
                ]
            );
        }
    }
}
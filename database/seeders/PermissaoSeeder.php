<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permissao;
use App\Models\Perfil;

class PermissaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar se as permissões já existem (criadas na migration)
        if (Permissao::count() > 0) {
            $this->command->info('✅ Permissões já existem na base de dados (criadas via migration)');
            return;
        }

        // Caso não existam, criar as permissões
        $permissoes = [
            // Clientes
            ['code' => 'clientes.view', 'label' => 'Visualizar Clientes', 'descricao' => 'Visualizar lista e detalhes de clientes'],
            ['code' => 'clientes.create', 'label' => 'Criar Clientes', 'descricao' => 'Criar novos clientes'],
            ['code' => 'clientes.edit', 'label' => 'Editar Clientes', 'descricao' => 'Editar dados de clientes'],
            ['code' => 'clientes.delete', 'label' => 'Excluir Clientes', 'descricao' => 'Excluir clientes do sistema'],

            // Contas
            ['code' => 'contas.view', 'label' => 'Visualizar Contas', 'descricao' => 'Visualizar lista e detalhes de contas'],
            ['code' => 'contas.create', 'label' => 'Criar Contas', 'descricao' => 'Criar novas contas bancárias'],
            ['code' => 'contas.edit', 'label' => 'Editar Contas', 'descricao' => 'Editar dados de contas'],
            ['code' => 'contas.delete', 'label' => 'Excluir Contas', 'descricao' => 'Excluir contas do sistema'],
            ['code' => 'contas.depositar', 'label' => 'Depositar', 'descricao' => 'Realizar depósitos em contas'],
            ['code' => 'contas.levantar', 'label' => 'Levantar', 'descricao' => 'Realizar levantamentos de contas'],

            // Transações
            ['code' => 'transacoes.view', 'label' => 'Visualizar Transações', 'descricao' => 'Visualizar histórico de transações'],
            ['code' => 'transacoes.create', 'label' => 'Criar Transações', 'descricao' => 'Realizar novas transações'],
            ['code' => 'transacoes.transferir', 'label' => 'Transferir', 'descricao' => 'Realizar transferências internas'],
            ['code' => 'transacoes.transferir_externo', 'label' => 'Transferir Externo', 'descricao' => 'Realizar transferências externas'],
            ['code' => 'transacoes.cambio', 'label' => 'Câmbio', 'descricao' => 'Realizar operações de câmbio'],

            // Cartões
            ['code' => 'cartoes.view', 'label' => 'Visualizar Cartões', 'descricao' => 'Visualizar lista e detalhes de cartões'],
            ['code' => 'cartoes.create', 'label' => 'Criar Cartões', 'descricao' => 'Criar novos cartões'],
            ['code' => 'cartoes.edit', 'label' => 'Editar Cartões', 'descricao' => 'Editar dados de cartões'],
            ['code' => 'cartoes.delete', 'label' => 'Excluir Cartões', 'descricao' => 'Excluir cartões do sistema'],
            ['code' => 'cartoes.bloquear', 'label' => 'Bloquear Cartões', 'descricao' => 'Bloquear/desbloquear cartões'],

            // Seguros
            ['code' => 'seguros.view', 'label' => 'Visualizar Seguros', 'descricao' => 'Visualizar apólices e sinistros'],
            ['code' => 'seguros.create', 'label' => 'Criar Seguros', 'descricao' => 'Criar novas apólices'],
            ['code' => 'seguros.edit', 'label' => 'Editar Seguros', 'descricao' => 'Editar apólices e sinistros'],
            ['code' => 'seguros.delete', 'label' => 'Excluir Seguros', 'descricao' => 'Excluir apólices do sistema'],

            // Taxas de Câmbio
            ['code' => 'cambio.view', 'label' => 'Visualizar Câmbio', 'descricao' => 'Visualizar taxas de câmbio'],
            ['code' => 'cambio.edit', 'label' => 'Editar Câmbio', 'descricao' => 'Atualizar taxas de câmbio'],

            // Relatórios
            ['code' => 'relatorios.dashboard', 'label' => 'Dashboard', 'descricao' => 'Visualizar dashboard e métricas'],
            ['code' => 'relatorios.transacoes', 'label' => 'Relatório Transações', 'descricao' => 'Gerar relatórios de transações'],
            ['code' => 'relatorios.extratos', 'label' => 'Extratos', 'descricao' => 'Gerar extratos de contas'],
            ['code' => 'relatorios.auditoria', 'label' => 'Auditoria', 'descricao' => 'Visualizar logs de auditoria'],

            // Usuários e Administração
            ['code' => 'usuarios.view', 'label' => 'Visualizar Usuários', 'descricao' => 'Visualizar lista de usuários'],
            ['code' => 'usuarios.create', 'label' => 'Criar Usuários', 'descricao' => 'Criar novos usuários'],
            ['code' => 'usuarios.edit', 'label' => 'Editar Usuários', 'descricao' => 'Editar dados de usuários'],
            ['code' => 'usuarios.delete', 'label' => 'Excluir Usuários', 'descricao' => 'Excluir usuários do sistema'],

            // Configurações
            ['code' => 'config.view', 'label' => 'Visualizar Configurações', 'descricao' => 'Visualizar configurações do sistema'],
            ['code' => 'config.edit', 'label' => 'Editar Configurações', 'descricao' => 'Alterar configurações do sistema'],
        ];

        foreach ($permissoes as $permissaoData) {
            Permissao::create($permissaoData);
        }

        $this->command->info('✅ Permissões criadas com sucesso! (' . count($permissoes) . ' permissões)');
    }
}
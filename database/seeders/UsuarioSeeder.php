<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar usuário administrador padrão
        $perfilAdmin = \App\Models\Perfil::where('nome', 'Administrador')->first();
        
        if ($perfilAdmin) {
            $admin = \App\Models\Usuario::firstOrCreate(
                ['email' => 'admin@banco.ao'],
                [
                    'nome' => 'Administrador do Sistema',
                    'email' => 'admin@banco.ao',
                    'senha' => bcrypt('admin123'), // Senha padrão - deve ser alterada em produção
                    'perfil_id' => $perfilAdmin->id,
                    'status_usuario' => 'ativo'
                ]
            );
        }

        // Criar usuário gerente de exemplo
        $perfilGerente = \App\Models\Perfil::where('nome', 'Gerente')->first();
        
        if ($perfilGerente) {
            $gerente = \App\Models\Usuario::firstOrCreate(
                ['email' => 'gerente@banco.ao'],
                [
                    'nome' => 'João Silva',
                    'email' => 'gerente@banco.ao',
                    'senha' => bcrypt('gerente123'),
                    'perfil_id' => $perfilGerente->id,
                    'status_usuario' => 'ativo'
                ]
            );
        }

        // Criar usuário atendente de exemplo
        $perfilAtendente = \App\Models\Perfil::where('nome', 'Atendente')->first();
        
        if ($perfilAtendente) {
            $atendente = \App\Models\Usuario::firstOrCreate(
                ['email' => 'atendente@banco.ao'],
                [
                    'nome' => 'Maria Santos',
                    'email' => 'atendente@banco.ao',
                    'senha' => bcrypt('atendente123'),
                    'perfil_id' => $perfilAtendente->id,
                    'status_usuario' => 'ativo'
                ]
            );
        }
    }
}

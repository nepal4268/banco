<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login do usuário
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'senha' => 'required|string',
        ]);

        $usuario = Usuario::where('email', $request->email)->first();

        if (!$usuario || !Hash::check($request->senha, $usuario->senha)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais fornecidas estão incorretas.'],
            ]);
        }

        if ($usuario->status_usuario !== 'ativo') {
            throw ValidationException::withMessages([
                'email' => ['Sua conta está inativa. Entre em contato com o administrador.'],
            ]);
        }

        // Criar token de acesso
        $token = $usuario->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Login realizado com sucesso',
            'user' => [
                'id' => $usuario->id,
                'nome' => $usuario->nome,
                'email' => $usuario->email,
                'perfil' => $usuario->perfil ? $usuario->perfil->nome : null,
                'status' => $usuario->status_usuario,
            ],
            'token' => $token,
        ]);
    }

    /**
     * Logout do usuário
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout realizado com sucesso'
        ]);
    }

    /**
     * Obter dados do usuário autenticado
     */
    public function me(Request $request): JsonResponse
    {
        $usuario = $request->user();
        $usuario->load('perfil');

        return response()->json([
            'user' => [
                'id' => $usuario->id,
                'nome' => $usuario->nome,
                'email' => $usuario->email,
                'perfil' => $usuario->perfil ? $usuario->perfil->nome : null,
                'status' => $usuario->status_usuario,
                'permissions' => $this->getUserPermissions($usuario),
            ]
        ]);
    }

    /**
     * Obter permissões do usuário
     */
    private function getUserPermissions(Usuario $usuario): array
    {
        $permissions = [];

        // Permissões diretas do usuário
        $userPermissions = $usuario->permissoes()->pluck('code')->toArray();
        
        // Permissões do perfil
        if ($usuario->perfil) {
            $profilePermissions = $usuario->perfil->permissoes()->pluck('code')->toArray();
            $permissions = array_merge($userPermissions, $profilePermissions);
        } else {
            $permissions = $userPermissions;
        }

        // Se tem permissão de admin.full, adicionar todas as permissões
        if (in_array('admin.full', $permissions)) {
            $allPermissions = \App\Models\Permissao::pluck('code')->toArray();
            $permissions = array_merge($permissions, $allPermissions);
        }

        return array_unique($permissions);
    }

    /**
     * Alterar senha
     */
    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'senha_atual' => 'required|string',
            'nova_senha' => 'required|string|min:6|confirmed',
        ]);

        $usuario = $request->user();

        if (!Hash::check($request->senha_atual, $usuario->senha)) {
            throw ValidationException::withMessages([
                'senha_atual' => ['A senha atual está incorreta.'],
            ]);
        }

        $usuario->update([
            'senha' => Hash::make($request->nova_senha)
        ]);

        return response()->json([
            'message' => 'Senha alterada com sucesso'
        ]);
    }
}

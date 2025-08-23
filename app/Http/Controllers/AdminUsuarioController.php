<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Perfil;
use App\Models\Agencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UsuarioStoreRequest;
use App\Http\Requests\UsuarioUpdateRequest;

class AdminUsuarioController extends Controller
{
    public function index(Request $request)
    {
        $query = Usuario::with(['perfil', 'agencia']);

        if ($request->filled('nome')) {
            $query->where('nome', 'like', '%' . $request->nome . '%');
        }

        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        if ($request->filled('perfil_id')) {
            $query->where('perfil_id', $request->perfil_id);
        }

        $usuarios = $query->paginate(15);
        $perfis = Perfil::all();
        $agencias = Agencia::all();

        return view('admin.usuarios.index', compact('usuarios', 'perfis', 'agencias'));
    }

    public function create()
    {
        $perfis = Perfil::all();
        $agencias = Agencia::all();

        return view('admin.usuarios.create', compact('perfis', 'agencias'));
    }

    public function store(UsuarioStoreRequest $request)
    {
        $data = $request->validated();
        $data['senha'] = Hash::make($data['senha']);
        $data['usuario_criacao'] = Auth::id();


        $usuario = Usuario::create($data);
        $usuario->load(['perfil', 'agencia']);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'usuario' => $usuario], 201);
        }

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuário criado com sucesso!');
    }

    public function show(Usuario $usuario, Request $request)
    {
        $usuario->load(['perfil', 'agencia']);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($usuario);
        }

        return view('admin.usuarios.show', compact('usuario'));
    }

    public function edit(Usuario $usuario, Request $request)
    {
        $perfis = Perfil::all();
        $agencias = Agencia::all();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(array_merge($usuario->toArray(), ['perfis' => $perfis, 'agencias' => $agencias]));
        }

        return view('admin.usuarios.edit', compact('usuario', 'perfis', 'agencias'));
    }

    public function update(UsuarioUpdateRequest $request, Usuario $usuario)
    {
        $data = $request->validated();

        if (!empty($data['senha'])) {
            $data['senha'] = Hash::make($data['senha']);
        } else {
            unset($data['senha']);
        }

        $data['usuario_atualizacao'] = Auth::id();

        $usuario->update($data);

        $usuario->load(['perfil', 'agencia']);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'usuario' => $usuario]);
        }

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuário atualizado com sucesso!');
    }

    public function destroy(Usuario $usuario, Request $request)
    {
        if ($usuario->id === Auth::id()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Não é possível excluir seu próprio usuário!'], 422);
            }

            return redirect()->route('admin.usuarios.index')->with('error', 'Não é possível excluir seu próprio usuário!');
        }

        $usuario->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuário excluído com sucesso!');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Perfil;
use App\Models\Permissao;
use Illuminate\Support\Facades\Auth;

class AdminPerfilController extends Controller
{
    public function index(Request $request)
    {
        $query = Perfil::with('permissoes');

        if ($request->filled('nome')) {
            $query->where('nome', 'like', '%' . $request->nome . '%');
        }

        $perfis = $query->paginate(15);

        return view('admin.perfis.index', compact('perfis'));
    }

    public function create()
    {
        $permissoes = Permissao::all();
        return view('admin.perfis.create', compact('permissoes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:100|unique:perfis,nome',
            'descricao' => 'nullable|string|max:500',
            'permissoes' => 'array',
            'permissoes.*' => 'exists:permissoes,id',
        ]);

        $data = $request->only(['nome', 'descricao']);
        $data['usuario_criacao'] = Auth::id();

        $perfil = Perfil::create($data);

        if ($request->has('permissoes')) {
            $perfil->permissoes()->sync($request->permissoes);
        }

        return redirect()->route('admin.perfis.index')->with('success', 'Perfil criado com sucesso!');
    }

    public function show(Perfil $perfil)
    {
        $perfil->load('permissoes');
        return view('admin.perfis.show', compact('perfil'));
    }

    public function edit(Perfil $perfil)
    {
        $permissoes = Permissao::all();
        $perfil->load('permissoes');
        return view('admin.perfis.edit', compact('perfil', 'permissoes'));
    }

    public function update(Request $request, Perfil $perfil)
    {
        $request->validate([
            'nome' => 'required|string|max:100|unique:perfis,nome,' . $perfil->id,
            'descricao' => 'nullable|string|max:500',
            'permissoes' => 'array',
            'permissoes.*' => 'exists:permissoes,id',
        ]);

        $data = $request->only(['nome', 'descricao']);
        $data['usuario_atualizacao'] = Auth::id();

        $perfil->update($data);

        if ($request->has('permissoes')) {
            $perfil->permissoes()->sync($request->permissoes);
        } else {
            $perfil->permissoes()->detach();
        }

        return redirect()->route('admin.perfis.index')->with('success', 'Perfil atualizado com sucesso!');
    }

    public function destroy(Perfil $perfil)
    {
        $perfil->delete();
        return redirect()->route('admin.perfis.index')->with('success', 'Perfil excluído com sucesso!');
    }
}

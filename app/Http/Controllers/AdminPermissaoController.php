<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permissao;

class AdminPermissaoController extends Controller
{
    public function index()
    {
        $permissoes = Permissao::paginate(20);
        return view('admin.permissoes.index', compact('permissoes'));
    }

    public function create()
    {
        return view('admin.permissoes.create');
    }

    public function store(Request $request)
    {
        // support legacy 'nome' field from older forms
        if (!$request->filled('label') && $request->filled('nome')) {
            $request->merge(['label' => $request->input('nome')]);
        }

        $request->validate([
            'code' => 'required|string|max:100|unique:permissoes,code',
            'label' => 'required|string|max:150',
            'descricao' => 'nullable|string|max:500',
        ]);

        Permissao::create($request->only(['code', 'label', 'descricao']));

        return redirect()->route('admin.permissoes.index')->with('success', 'Permissão criada');
    }

    public function show(Permissao $permissao)
    {
        return view('admin.permissoes.show', compact('permissao'));
    }

    public function edit(Permissao $permissao)
    {
        return view('admin.permissoes.edit', compact('permissao'));
    }

    public function update(Request $request, Permissao $permissao)
    {
        // support legacy 'nome' field
        if (!$request->filled('label') && $request->filled('nome')) {
            $request->merge(['label' => $request->input('nome')]);
        }

        $request->validate([
            'code' => 'required|string|max:100|unique:permissoes,code,' . $permissao->id,
            'label' => 'required|string|max:150',
            'descricao' => 'nullable|string|max:500',
        ]);

        $permissao->update($request->only(['code', 'label', 'descricao']));

        return redirect()->route('admin.permissoes.index')->with('success', 'Permissão atualizada');
    }

    public function destroy(Permissao $permissao)
    {
        $permissao->delete();
        return redirect()->route('admin.permissoes.index')->with('success', 'Permissão excluída');
    }
}

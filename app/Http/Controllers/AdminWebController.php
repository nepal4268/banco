<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Agencia;
use App\Models\Perfil;
use App\Models\Permissao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminWebController extends Controller
{
    public function index(Request $request)
    {
        // Determina qual recurso está sendo acessado baseado na rota
        $resource = $request->route()->getName();
        
        if (str_contains($resource, 'usuarios')) {
            return $this->usuariosIndex($request);
        } elseif (str_contains($resource, 'agencias')) {
            return $this->agenciasIndex($request);
        } elseif (str_contains($resource, 'perfis')) {
            return $this->perfisIndex($request);
        }
        
        abort(404);
    }

    public function create(Request $request)
    {
        $resource = $request->route()->getName();
        
        if (str_contains($resource, 'usuarios')) {
            return $this->usuariosCreate();
        } elseif (str_contains($resource, 'agencias')) {
            return $this->agenciasCreate();
        } elseif (str_contains($resource, 'perfis')) {
            return $this->perfisCreate();
        }
        
        abort(404);
    }

    public function store(Request $request)
    {
        $resource = $request->route()->getName();
        
        if (str_contains($resource, 'usuarios')) {
            return $this->usuariosStore($request);
        } elseif (str_contains($resource, 'agencias')) {
            return $this->agenciasStore($request);
        } elseif (str_contains($resource, 'perfis')) {
            return $this->perfisStore($request);
        }
        
        abort(404);
    }

    public function show($id, Request $request)
    {
        $resource = $request->route()->getName();
        
        if (str_contains($resource, 'usuarios')) {
            return $this->usuariosShow(Usuario::findOrFail($id));
        } elseif (str_contains($resource, 'agencias')) {
            return $this->agenciasShow(Agencia::findOrFail($id));
        } elseif (str_contains($resource, 'perfis')) {
            return $this->perfisShow(Perfil::findOrFail($id));
        }
        
        abort(404);
    }

    public function edit($id, Request $request)
    {
        $resource = $request->route()->getName();
        
        if (str_contains($resource, 'usuarios')) {
            return $this->usuariosEdit(Usuario::findOrFail($id));
        } elseif (str_contains($resource, 'agencias')) {
            return $this->agenciasEdit(Agencia::findOrFail($id));
        } elseif (str_contains($resource, 'perfis')) {
            return $this->perfisEdit(Perfil::findOrFail($id));
        }
        
        abort(404);
    }

    public function update(Request $request, $id)
    {
        $resource = $request->route()->getName();
        
        if (str_contains($resource, 'usuarios')) {
            return $this->usuariosUpdate($request, Usuario::findOrFail($id));
        } elseif (str_contains($resource, 'agencias')) {
            return $this->agenciasUpdate($request, Agencia::findOrFail($id));
        } elseif (str_contains($resource, 'perfis')) {
            return $this->perfisUpdate($request, Perfil::findOrFail($id));
        }
        
        abort(404);
    }

    public function destroy($id, Request $request)
    {
        $resource = $request->route()->getName();
        
        if (str_contains($resource, 'usuarios')) {
            return $this->usuariosDestroy(Usuario::findOrFail($id));
        } elseif (str_contains($resource, 'agencias')) {
            return $this->agenciasDestroy(Agencia::findOrFail($id));
        } elseif (str_contains($resource, 'perfis')) {
            return $this->perfisDestroy(Perfil::findOrFail($id));
        }
        
        abort(404);
    }

    // Métodos para Usuários
    private function usuariosIndex(Request $request)
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

    private function usuariosCreate()
    {
        $perfis = Perfil::all();
        $agencias = Agencia::all();
        
        return view('admin.usuarios.create', compact('perfis', 'agencias'));
    }

    private function usuariosStore(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email',
            'senha' => 'required|string|min:6',
            'perfil_id' => 'required|exists:perfis,id',
            'agencia_id' => 'nullable|exists:agencias,id',
            'bi' => 'required|string|max:20|unique:usuarios,bi',
            'sexo' => 'required|in:M,F',
            'telefone' => 'required|string|max:20',
            'data_nascimento' => 'required|date',
            'endereco' => 'nullable|string|max:500',
            'cidade' => 'nullable|string|max:100',
            'provincia' => 'nullable|string|max:100',
        ]);

        $data = $request->all();
        $data['senha'] = Hash::make($data['senha']);
        $data['usuario_criacao'] = Auth::id();

        Usuario::create($data);

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuário criado com sucesso!');
    }

    private function usuariosShow(Usuario $usuario)
    {
        $usuario->load(['perfil', 'agencia']);
        
        return view('admin.usuarios.show', compact('usuario'));
    }

    private function usuariosEdit(Usuario $usuario)
    {
        $perfis = Perfil::all();
        $agencias = Agencia::all();
        
        return view('admin.usuarios.edit', compact('usuario', 'perfis', 'agencias'));
    }

    private function usuariosUpdate(Request $request, Usuario $usuario)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email,' . $usuario->id,
            'senha' => 'nullable|string|min:6',
            'perfil_id' => 'required|exists:perfis,id',
            'agencia_id' => 'nullable|exists:agencias,id',
            'bi' => 'required|string|max:20|unique:usuarios,bi,' . $usuario->id,
            'sexo' => 'required|in:M,F',
            'telefone' => 'required|string|max:20',
            'data_nascimento' => 'required|date',
            'endereco' => 'nullable|string|max:500',
            'cidade' => 'nullable|string|max:100',
            'provincia' => 'nullable|string|max:100',
        ]);

        $data = $request->all();
        
        if ($request->filled('senha')) {
            $data['senha'] = Hash::make($data['senha']);
        } else {
            unset($data['senha']);
        }
        
        $data['usuario_atualizacao'] = Auth::id();

        $usuario->update($data);

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuário atualizado com sucesso!');
    }

    private function usuariosDestroy(Usuario $usuario)
    {
        if ($usuario->id === Auth::id()) {
            return redirect()->route('admin.usuarios.index')->with('error', 'Não é possível excluir seu próprio usuário!');
        }

        $usuario->delete();
        
        return redirect()->route('admin.usuarios.index')->with('success', 'Usuário excluído com sucesso!');
    }

    // Métodos para Agências
    private function agenciasIndex(Request $request)
    {
        $query = Agencia::query();

        if ($request->filled('nome')) {
            $query->where('nome', 'like', '%' . $request->nome . '%');
        }

        if ($request->filled('cidade')) {
            $query->where('cidade', 'like', '%' . $request->cidade . '%');
        }

        $agencias = $query->paginate(15);

        return view('admin.agencias.index', compact('agencias'));
    }

    private function agenciasCreate()
    {
        return view('admin.agencias.create');
    }

    private function agenciasStore(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'endereco' => 'required|string|max:500',
            'cidade' => 'required|string|max:100',
            'provincia' => 'required|string|max:100',
            'telefone' => 'required|string|max:20',
            'email' => 'required|email|unique:agencias,email',
        ]);

        $data = $request->all();
        $data['usuario_criacao'] = Auth::id();

        Agencia::create($data);

        return redirect()->route('admin.agencias.index')->with('success', 'Agência criada com sucesso!');
    }

    private function agenciasShow(Agencia $agencia)
    {
        return view('admin.agencias.show', compact('agencia'));
    }

    private function agenciasEdit(Agencia $agencia)
    {
        return view('admin.agencias.edit', compact('agencia'));
    }

    private function agenciasUpdate(Request $request, Agencia $agencia)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'endereco' => 'required|string|max:500',
            'cidade' => 'required|string|max:100',
            'provincia' => 'required|string|max:100',
            'telefone' => 'required|string|max:20',
            'email' => 'required|email|unique:agencias,email,' . $agencia->id,
        ]);

        $data = $request->all();
        $data['usuario_atualizacao'] = Auth::id();

        $agencia->update($data);

        return redirect()->route('admin.agencias.index')->with('success', 'Agência atualizada com sucesso!');
    }

    private function agenciasDestroy(Agencia $agencia)
    {
        $agencia->delete();
        
        return redirect()->route('admin.agencias.index')->with('success', 'Agência excluída com sucesso!');
    }

    // Métodos para Perfis
    private function perfisIndex(Request $request)
    {
        $query = Perfil::with('permissoes');

        if ($request->filled('nome')) {
            $query->where('nome', 'like', '%' . $request->nome . '%');
        }

        $perfis = $query->paginate(15);

        return view('admin.perfis.index', compact('perfis'));
    }

    private function perfisCreate()
    {
        $permissoes = Permissao::all();
        
        return view('admin.perfis.create', compact('permissoes'));
    }

    private function perfisStore(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:100|unique:perfis,nome',
            'descricao' => 'nullable|string|max:500',
            'permissoes' => 'array',
            'permissoes.*' => 'exists:permissoes,id',
        ]);

        $data = $request->all();
        $data['usuario_criacao'] = Auth::id();

        $perfil = Perfil::create($data);

        if ($request->has('permissoes')) {
            $perfil->permissoes()->attach($request->permissoes);
        }

        return redirect()->route('admin.perfis.index')->with('success', 'Perfil criado com sucesso!');
    }

    private function perfisShow(Perfil $perfil)
    {
        $perfil->load('permissoes');
        
        return view('admin.perfis.show', compact('perfil'));
    }

    private function perfisEdit(Perfil $perfil)
    {
        $permissoes = Permissao::all();
        $perfil->load('permissoes');
        
        return view('admin.perfis.edit', compact('perfil', 'permissoes'));
    }

    private function perfisUpdate(Request $request, Perfil $perfil)
    {
        $request->validate([
            'nome' => 'required|string|max:100|unique:perfis,nome,' . $perfil->id,
            'descricao' => 'nullable|string|max:500',
            'permissoes' => 'array',
            'permissoes.*' => 'exists:permissoes,id',
        ]);

        $data = $request->all();
        $data['usuario_atualizacao'] = Auth::id();

        $perfil->update($data);

        if ($request->has('permissoes')) {
            $perfil->permissoes()->sync($request->permissoes);
        } else {
            $perfil->permissoes()->detach();
        }

        return redirect()->route('admin.perfis.index')->with('success', 'Perfil atualizado com sucesso!');
    }

    private function perfisDestroy(Perfil $perfil)
    {
        $perfil->delete();
        
        return redirect()->route('admin.perfis.index')->with('success', 'Perfil excluído com sucesso!');
    }
}

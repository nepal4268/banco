<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Agencia;
use App\Models\Perfil;
use App\Models\Permissao;
use Illuminate\Http\Request;
use App\Http\Requests\UsuarioStoreRequest;
use App\Http\Requests\UsuarioUpdateRequest;
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
    // Os handlers de usuário foram movidos para um controller dedicado: AdminUsuarioController

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
            'telefone' => 'required|string|max:500',
            'email' => 'required|email|unique:agencias,email',
        ]);

        $data = $request->all();
        $data['usuario_criacao'] = Auth::id();

        // Normalize telefone: accept comma-separated string and store as array to match cast
        if (isset($data['telefone'])) {
            if (is_string($data['telefone'])) {
                $telefones = array_filter(array_map('trim', explode(',', $data['telefone'])));
                $data['telefone'] = array_values($telefones);
            }
        }

        Agencia::create($data);

        return redirect()->route('admin.agencias.index')->with('success', 'Agência criada com sucesso!');
    }

    private function agenciasShow(Agencia $agencia)
    {
        return view('admin.agencias.show', compact('agencia'));
    }

    // Dashboard por agência: resumo e métricas
    public function agenciasDashboard(Request $request, Agencia $agencia)
    {
        // resumo simples
        $totalContas = $agencia->contas()->count();
        $saldoTotal = $agencia->contas()->sum('saldo');

        // preparar filtros: período (start, end) e tipo (opcional)
    $end = $request->query('end') ? \Carbon\Carbon::parse($request->query('end'))->endOfDay() : \Carbon\Carbon::now()->endOfDay();
    $start = $request->query('start') ? \Carbon\Carbon::parse($request->query('start'))->startOfDay() : (clone $end)->subMonths(11)->startOfMonth();

        $contaIds = $agencia->contas()->pluck('id')->toArray();

        // base query para transacoes envolvendo a agência
        $baseQuery = \App\Models\Transacao::query()
            ->where(function ($q) use ($contaIds) {
                $q->whereIn('conta_origem_id', $contaIds)
                  ->orWhereIn('conta_destino_id', $contaIds);
            })
            ->whereBetween('created_at', [$start, $end]);

        // métricas agregadas
        $totalTransacoes = (clone $baseQuery)->count();
        $totalEntradas = (clone $baseQuery)->whereIn('conta_destino_id', $contaIds)->sum('valor');
        $totalSaidas = (clone $baseQuery)->whereIn('conta_origem_id', $contaIds)->sum('valor');

        // série mensal para os últimos 12 meses no intervalo solicitado
        $periodStart = (clone $start)->startOfMonth();
        $periodEnd = (clone $end)->endOfMonth();

        $months = [];
        $cursor = (clone $periodStart);
        while ($cursor->lte($periodEnd)) {
            $months[] = $cursor->format('Y-m');
            $cursor->addMonth();
        }

        $seriesTotal = [];
        $seriesEntradas = [];
        $seriesSaidas = [];

        foreach ($months as $m) {
            [$y, $mo] = explode('-', $m);
            $from = \Carbon\Carbon::createFromDate($y, $mo, 1)->startOfDay();
            $to = (clone $from)->endOfMonth()->endOfDay();

            $q = \App\Models\Transacao::query()
                ->where(function ($q2) use ($contaIds) {
                    $q2->whereIn('conta_origem_id', $contaIds)
                       ->orWhereIn('conta_destino_id', $contaIds);
                })
                ->whereBetween('created_at', [$from, $to]);

            $seriesTotal[] = $q->count();
            $seriesEntradas[] = (clone $q)->whereIn('conta_destino_id', $contaIds)->sum('valor');
            $seriesSaidas[] = (clone $q)->whereIn('conta_origem_id', $contaIds)->sum('valor');
        }

        return view('admin.agencias.dashboard', compact(
            'agencia', 'totalContas', 'saldoTotal', 'totalTransacoes', 'totalEntradas', 'totalSaidas',
            'months', 'seriesTotal', 'seriesEntradas', 'seriesSaidas', 'start', 'end'
        ));
    }

    // Retorna dados do dashboard em JSON usando agregações otimizadas
    public function agenciasDashboardData(Request $request, Agencia $agencia)
    {
    $end = $request->query('end') ? \Carbon\Carbon::parse($request->query('end'))->endOfDay() : \Carbon\Carbon::now()->endOfDay();
    $start = $request->query('start') ? \Carbon\Carbon::parse($request->query('start'))->startOfDay() : (clone $end)->subMonths(11)->startOfMonth();

        $contaIds = $agencia->contas()->pluck('id')->toArray();
        if (empty($contaIds)) {
            return response()->json([
                'months' => [], 'total' => 0, 'entradas' => 0, 'saidas' => 0,
                'seriesTotal' => [], 'seriesEntradas' => [], 'seriesSaidas' => [],
            ]);
        }

        // Usar query raw para agrupar por ano-mês e agregar counts/sums para origem/destino
        $bindings = [implode(',', $contaIds), $start->format('Y-m-d H:i:s'), $end->format('Y-m-d H:i:s')];

        // MySQL specific: use DATE_FORMAT on created_at to bucket by year-month
        $sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') as ym,
                        COUNT(*) as total,
                        SUM(CASE WHEN conta_destino_id IN (?) THEN valor ELSE 0 END) as entradas,
                        SUM(CASE WHEN conta_origem_id IN (?) THEN valor ELSE 0 END) as saidas
                 FROM transacoes
                 WHERE (conta_origem_id IN (?) OR conta_destino_id IN (?))
                   AND created_at BETWEEN ? AND ?
                 GROUP BY ym
                 ORDER BY ym ASC";

        // Note: bindings repeated for the IN (?) placeholders; we'll pass arrays as string and rely on cast in SQL expression
        // To keep it simple and compatible, fallback to Eloquent grouping if raw approach fails for the DB driver
        try {
            $rows = \DB::select($sql, [$contaIds, $contaIds, $contaIds, $contaIds, $start->format('Y-m-d H:i:s'), $end->format('Y-m-d H:i:s')]);
        } catch (\Exception $e) {
            // Fallback: build month buckets via Eloquent (less efficient)
            $periodStart = (clone $start)->startOfMonth();
            $periodEnd = (clone $end)->endOfMonth();
            $months = [];
            $cursor = (clone $periodStart);
            $seriesTotal = $seriesEntradas = $seriesSaidas = [];
            while ($cursor->lte($periodEnd)) {
                $from = (clone $cursor)->startOfMonth();
                $to = (clone $cursor)->endOfMonth();
                $q = \App\Models\Transacao::where(function($q2) use ($contaIds){
                    $q2->whereIn('conta_origem_id', $contaIds)->orWhereIn('conta_destino_id', $contaIds);
                })->whereBetween('created_at', [$from, $to]);
                $months[] = $cursor->format('Y-m');
                $seriesTotal[] = $q->count();
                $seriesEntradas[] = (clone $q)->whereIn('conta_destino_id', $contaIds)->sum('valor');
                $seriesSaidas[] = (clone $q)->whereIn('conta_origem_id', $contaIds)->sum('valor');
                $cursor->addMonth();
            }

            $total = array_sum($seriesTotal);
            $entradas = array_sum($seriesEntradas);
            $saidas = array_sum($seriesSaidas);

            return response()->json(compact('months','seriesTotal','seriesEntradas','seriesSaidas','total','entradas','saidas'));
        }

        $months = [];
        $seriesTotal = $seriesEntradas = $seriesSaidas = [];
        $total = 0; $entradas = 0; $saidas = 0;
        foreach ($rows as $r) {
            $months[] = $r->ym;
            $seriesTotal[] = (int) $r->total;
            $seriesEntradas[] = (float) $r->entradas;
            $seriesSaidas[] = (float) $r->saidas;
            $total += (int) $r->total;
            $entradas += (float) $r->entradas;
            $saidas += (float) $r->saidas;
        }

        return response()->json(compact('months','seriesTotal','seriesEntradas','seriesSaidas','total','entradas','saidas'));
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
            'telefone' => 'required|string|max:500',
            'email' => 'required|email|unique:agencias,email,' . $agencia->id,
        ]);

        $data = $request->all();
        $data['usuario_atualizacao'] = Auth::id();

        // Normalize telefone before update
        if (isset($data['telefone'])) {
            if (is_string($data['telefone'])) {
                $telefones = array_filter(array_map('trim', explode(',', $data['telefone'])));
                $data['telefone'] = array_values($telefones);
            }
        }

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

<?php

namespace App\Http\Controllers;

use App\Models\Apolice;
use App\Models\Sinistro;
use App\Models\Cliente;
use App\Models\TipoSeguro;
use App\Models\StatusApolice;
use App\Models\StatusSinistro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SeguroWebController extends Controller
{
    public function index(Request $request)
    {
        $query = Apolice::with(['cliente', 'tipoSeguro', 'statusApolice']);

        // Filtros
        if ($request->filled('numero_apolice')) {
            $query->where('numero_apolice', 'like', '%' . $request->numero_apolice . '%');
        }

        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->cliente_id);
        }

        if ($request->filled('tipo_seguro_id')) {
            $query->where('tipo_seguro_id', $request->tipo_seguro_id);
        }

        if ($request->filled('status_apolice_id')) {
            $query->where('status_apolice_id', $request->status_apolice_id);
        }

        $apolices = $query->orderBy('created_at', 'desc')->paginate(15);
        $clientes = Cliente::all();
        $tiposSeguro = TipoSeguro::all();
        $statusApolice = StatusApolice::all();

        return view('admin.seguros.apolices.index', compact('apolices', 'clientes', 'tiposSeguro', 'statusApolice'));
    }

    public function create()
    {
        $clientes = Cliente::all();
        $tiposSeguro = TipoSeguro::all();
        $statusApolice = StatusApolice::all();
        
        return view('admin.seguros.apolices.create', compact('clientes', 'tiposSeguro', 'statusApolice'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'tipo_seguro_id' => 'required|exists:tipos_seguro,id',
            'numero_apolice' => 'required|string|max:50|unique:apolices,numero_apolice',
            'inicio_vigencia' => 'required|date',
            'fim_vigencia' => 'required|date|after:inicio_vigencia',
            'status_apolice_id' => 'required|exists:status_apolice,id',
            'premio_mensal' => 'required|numeric|min:0',
        ]);

        $data = $request->all();
        $data['usuario_criacao'] = Auth::id();

        Apolice::create($data);

        return redirect()->route('apolices.index')->with('success', 'Apólice criada com sucesso!');
    }

    public function show(Apolice $apolice)
    {
        $apolice->load(['cliente', 'tipoSeguro', 'statusApolice', 'sinistros.statusSinistro']);
        
        return view('admin.seguros.apolices.show', compact('apolice'));
    }

    public function edit(Apolice $apolice)
    {
        $clientes = Cliente::all();
        $tiposSeguro = TipoSeguro::all();
        $statusApolice = StatusApolice::all();
        
        return view('admin.seguros.apolices.edit', compact('apolice', 'clientes', 'tiposSeguro', 'statusApolice'));
    }

    public function update(Request $request, Apolice $apolice)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'tipo_seguro_id' => 'required|exists:tipos_seguro,id',
            'numero_apolice' => 'required|string|max:50|unique:apolices,numero_apolice,' . $apolice->id,
            'inicio_vigencia' => 'required|date',
            'fim_vigencia' => 'required|date|after:inicio_vigencia',
            'status_apolice_id' => 'required|exists:status_apolice,id',
            'premio_mensal' => 'required|numeric|min:0',
        ]);

        $data = $request->all();
        $data['usuario_atualizacao'] = Auth::id();

        $apolice->update($data);

        return redirect()->route('apolices.index')->with('success', 'Apólice atualizada com sucesso!');
    }

    public function destroy(Apolice $apolice)
    {
        $apolice->delete();
        
        return redirect()->route('apolices.index')->with('success', 'Apólice excluída com sucesso!');
    }

    // Métodos para Sinistros
    public function sinistrosIndex(Request $request)
    {
        $query = Sinistro::with(['apolice.cliente', 'statusSinistro']);

        // Filtros
        if ($request->filled('apolice_id')) {
            $query->where('apolice_id', $request->apolice_id);
        }

        if ($request->filled('status_sinistro_id')) {
            $query->where('status_sinistro_id', $request->status_sinistro_id);
        }

        if ($request->filled('data_inicio')) {
            $query->whereDate('data_sinistro', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('data_sinistro', '<=', $request->data_fim);
        }

        $sinistros = $query->orderBy('created_at', 'desc')->paginate(15);
        $apolices = Apolice::with('cliente')->get();
        $statusSinistro = StatusSinistro::all();

        return view('admin.seguros.sinistros.index', compact('sinistros', 'apolices', 'statusSinistro'));
    }

    public function sinistrosCreate()
    {
        $apolices = Apolice::with('cliente')->get();
        $statusSinistro = StatusSinistro::all();
        
        return view('admin.seguros.sinistros.create', compact('apolices', 'statusSinistro'));
    }

    public function sinistrosStore(Request $request)
    {
        $request->validate([
            'apolice_id' => 'required|exists:apolices,id',
            'descricao' => 'required|string|max:1000',
            'valor_reivindicado' => 'required|numeric|min:0',
            'valor_pago' => 'nullable|numeric|min:0',
            'data_sinistro' => 'required|date',
            'status_sinistro_id' => 'required|exists:status_sinistro,id',
        ]);

        $data = $request->all();
        $data['usuario_criacao'] = Auth::id();

        Sinistro::create($data);

        return redirect()->route('sinistros.index')->with('success', 'Sinistro registrado com sucesso!');
    }

    public function sinistrosShow(Sinistro $sinistro)
    {
        $sinistro->load(['apolice.cliente', 'statusSinistro']);
        
        return view('admin.seguros.sinistros.show', compact('sinistro'));
    }

    public function sinistrosEdit(Sinistro $sinistro)
    {
        $apolices = Apolice::with('cliente')->get();
        $statusSinistro = StatusSinistro::all();
        
        return view('admin.seguros.sinistros.edit', compact('sinistro', 'apolices', 'statusSinistro'));
    }

    public function sinistrosUpdate(Request $request, Sinistro $sinistro)
    {
        $request->validate([
            'apolice_id' => 'required|exists:apolices,id',
            'descricao' => 'required|string|max:1000',
            'valor_reivindicado' => 'required|numeric|min:0',
            'valor_pago' => 'nullable|numeric|min:0',
            'data_sinistro' => 'required|date',
            'status_sinistro_id' => 'required|exists:status_sinistro,id',
        ]);

        $data = $request->all();
        $data['usuario_atualizacao'] = Auth::id();

        $sinistro->update($data);

        return redirect()->route('sinistros.index')->with('success', 'Sinistro atualizado com sucesso!');
    }

    public function sinistrosDestroy(Sinistro $sinistro)
    {
        $sinistro->delete();
        
        return redirect()->route('sinistros.index')->with('success', 'Sinistro excluído com sucesso!');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;

class AdminLookupController extends Controller
{
    protected function resolveConfig($key)
    {
        $cfg = Config::get('lookups.' . $key);
        if (!$cfg) abort(404);
        return $cfg;
    }

    public function index(Request $request, $key)
    {
        $cfg = $this->resolveConfig($key);
        $model = $cfg['model'];
        $items = $model::paginate(20);
        return view('admin.lookups.index', compact('items', 'cfg', 'key'));
    }

    public function create($key)
    {
        $cfg = $this->resolveConfig($key);
        return view('admin.lookups.create', compact('cfg', 'key'));
    }

    public function store(Request $request, $key)
    {
        $cfg = $this->resolveConfig($key);
        $model = $cfg['model'];
        $field = $cfg['field'] ?? 'name';

        $request->validate([$field => 'required|string|max:255']);
        $model::create([$field => $request->input($field)]);

        return Redirect::route('admin.lookups.index', $key)->with('success', $cfg['title'] . ' criada');
    }

    public function edit($key, $id)
    {
        $cfg = $this->resolveConfig($key);
        $model = $cfg['model'];
        $item = $model::findOrFail($id);
        return view('admin.lookups.edit', compact('cfg', 'key', 'item'));
    }

    public function update(Request $request, $key, $id)
    {
        $cfg = $this->resolveConfig($key);
        $model = $cfg['model'];
        $field = $cfg['field'] ?? 'name';

        $request->validate([$field => 'required|string|max:255']);
        $item = $model::findOrFail($id);
        $item->update([$field => $request->input($field)]);

        return Redirect::route('admin.lookups.index', $key)->with('success', $cfg['title'] . ' atualizada');
    }

    public function destroy($key, $id)
    {
        $cfg = $this->resolveConfig($key);
        $model = $cfg['model'];
        $item = $model::findOrFail($id);
        $item->delete();
        return Redirect::route('admin.lookups.index', $key)->with('success', $cfg['title'] . ' excluída');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\PasswordUpdateRequest;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile.index');
    }

    public function update(ProfileUpdateRequest $request)
    {
        $user = auth()->user();
        
        $data = $request->validated();
        
        if (isset($data['telefone'])) {
            $data['telefone'] = [$data['telefone']];
        }
        
        $user->update($data);

        return redirect()->route('profile.index')
            ->with('success', 'Perfil atualizado com sucesso!');
    }

    public function updatePassword(PasswordUpdateRequest $request)
    {
        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->senha)) {
            return back()->withErrors(['current_password' => 'A senha atual está incorreta.']);
        }

        $user->update([
            'senha' => Hash::make($request->password)
        ]);

        return redirect()->route('profile.index')
            ->with('success', 'Senha alterada com sucesso!');
    }
}

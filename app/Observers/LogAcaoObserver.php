<?php

namespace App\Observers;

use App\Models\LogAcao;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class LogAcaoObserver
{
    /**
     * Handle the LogAcao "creating" event.
     */
    public function creating(LogAcao $logAcao): void
    {
        // Auto-preencher campos se não foram fornecidos
        if (!$logAcao->usuario_id && Auth::check()) {
            $logAcao->usuario_id = Auth::id();
        }

        if (!$logAcao->ip_origem) {
            $logAcao->ip_origem = Request::ip();
        }

        // Definir created_at se não foi definido
        if (!$logAcao->created_at) {
            $logAcao->created_at = now();
        }
    }
}
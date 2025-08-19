<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Cliente;
use App\Models\Conta;
use App\Models\Transacao;
use App\Models\Pagamento;
use App\Models\LogAcao;
use App\Observers\ClienteObserver;
use App\Observers\ContaObserver;
use App\Observers\TransacaoObserver;
use App\Observers\PagamentoObserver;
use App\Observers\LogAcaoObserver;

class ObserverServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Registrar observers para auditoria automática
        Cliente::observe(ClienteObserver::class);
        Conta::observe(ContaObserver::class);
        Transacao::observe(TransacaoObserver::class);
        Pagamento::observe(PagamentoObserver::class);
        LogAcao::observe(LogAcaoObserver::class);
    }
}
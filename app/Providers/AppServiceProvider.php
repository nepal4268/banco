<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Definir constante usada pelo l5-swagger para o servidor
        if (!defined('L5_SWAGGER_CONST_HOST')) {
            $configured = env('L5_SWAGGER_CONST_HOST');
            $host = $configured ?: (string) config('app.url');

            // Se APP_URL não estiver configurada de forma útil, tentar detectar do request
            if (!$configured && ($host === 'http://localhost' || str_contains($host, 'my-default-host.com'))) {
                try {
                    $detected = Request::getSchemeAndHttpHost();
                    if (!empty($detected)) {
                        $host = $detected;
                    }
                } catch (\Throwable $e) {
                    // Ignorar e manter o host atual
                }
            }

            define('L5_SWAGGER_CONST_HOST', rtrim($host, '/'));
        }
    }
}

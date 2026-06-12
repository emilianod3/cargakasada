<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',

        // 🚀 ADICIONE O MÉTODO 'then' PARA AS SUAS ROTAS CUSTOMIZADAS:
        then: function () {

            // 2. Seus módulos customizados usando o middleware 'web'
            Route::middleware('web')->group(function () {
                Route::group([], base_path('routes/auth/routeauth.php'));
                Route::group([], base_path('routes/core/routecore.php'));
                //Route::group([], base_path('routes/auxiliares/routeauxiliares.php'));
                //Route::group([], base_path('routes/cadastro/routecadastro.php'));
                //Route::group([], base_path('routes/controle/routecontrole.php'));
                
            });
        },        
    )
    /*->withMiddleware(function (Middleware $middleware): void {
        //
    })*/
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
        ]);
        // 🚀 Registra um apelido (alias) para o seu middleware manual
        $middleware->alias([
            'authcheck' => \App\Http\Middleware\AuthenticateCheck::class,
            'redirectlogado' => \App\Http\Middleware\RedirectLogado::class,
        ]);

        $middleware->redirectTo(fn () => route('login'));        
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

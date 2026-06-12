<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectLogado
{
    public function handle(Request $request, Closure $next): Response
    {
        // 🚀 SE a sessão manual do utilizador EXISTIR, ele já está logado!
        if ($request->session()->has('user')) {
            // Redireciona imediatamente para o painel e impede de ver o login
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
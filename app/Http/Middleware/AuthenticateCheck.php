<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateCheck
{
    public function handle(Request $request, Closure $next): Response
    {
        // 🔒 Se a sua variável manual NÃO existir na sessão, manda o usuário pro login
        if (!$request->session()->has('user')) {
            
            // Se for uma requisição Inertia, manda um status limpo pro Vue interceptar
            if ($request->hasHeader('X-Inertia')) {
                return response()->json(['message' => 'Sessão Expirada.'], 419);
            }
            
            return redirect()->route('login');
        }

        return $next($request);
    }
}
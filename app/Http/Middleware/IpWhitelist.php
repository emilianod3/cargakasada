<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IpWhitelist
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
            // 1. IP do cliente tentando o acesso
        $clientIp = trim($request->ip());

        // 2. Proteção Loopback Integrada: Libera localhost (IPv4 e IPv6) nativamente
        if ($clientIp === '127.0.0.1' || $clientIp === '::1') {
            return $next($request);
        }

        // 3. Obter a lista de IPs permitidos do arquivo .env (limpando espaços extras)
        $allowedIps = array_map('trim', explode(',', env('ALLOWED_IPS', '')));

        // Se tem '*.*.*.*' na lista, libera geral
        if (in_array('*.*.*.*', $allowedIps)) {
            return $next($request);
        }

        // Função interna para checar se IP bate no padrão (com *)
        $ipMatchesPattern = function ($ip, $pattern) {
            // Escapa caracteres para regex
            $regex = preg_quote($pattern, '/');

            // Substitui \* pelo padrão de grupo generico para IPv4 e IPv6
            $regex = str_replace('\*', '[0-9a-fA-F:.]{1,}', $regex);

            // Delimitar regex, obrigando a começar e terminar de forma exata
            $regex = '/^' . $regex . '$/i';

            return preg_match($regex, $ip) === 1;
        };

        // Testar o IP contra cada padrão cadastrado no .env
        foreach ($allowedIps as $pattern) {
            if (!empty($pattern) && $ipMatchesPattern($clientIp, $pattern)) {
                return $next($request);
            }
        }

        // Se não passou por loopback nem pelos padrões do .env, bloqueia
        abort(403, 'Acesso negado.');
    }
}

<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        /*return [
            ...parent::share($request),
            //
        ];*/
        //$serverIp = $_SERVER['SERVER_ADDR'] ?? request()->server('SERVER_ADDR') ?? '127.0.0.1';
        $serverIp = $request->server('SERVER_ADDR', '127.0.0.1');
        
        return array_merge(parent::share($request), [
            'versions' => [
                'laravel_version' => \Illuminate\Foundation\Application::VERSION,
                'php_version' => PHP_VERSION,
            ],
            'auth' => [
                //'user' => $request->user(),
                'user'   => $request->session()->get('user'), // null se deslogado
                'grupo' => $request->session()->get('grupo'),  // null se deslogado
            ],
            'sistema' => [
                'versao'    => config('version.number', '1.0.0'), // Fallback caso não exista
                'hash'      => config('version.hash', '0000000'),
                'atualizado' => config('version.timestamp', ''),
                'serverIp' => $serverIp,
                'clientIp' => $request->ip(),
                'soServidor' => PHP_OS_FAMILY,
                'soServidorDetalhado' => php_uname('s') . ' ' . php_uname('r'),
                'composerVersion'    => $this->executarComandoServidor('composer --version'),
                'nodeVersion'        => $this->executarComandoServidor('node -v'),
                'libreOfficeVersion' => $this->executarComandoServidor('libreoffice --version', 'soffice --version'),
            ],
            'app_url' => config('app.url'),
            'app_debug' => (bool) env('APP_DEBUG', false),
            'storagepath' => public_path('storage'), //Storage::path('public'),
            'SISTEMA_CONTEXTBTNDIR' => (bool) env('SISTEMA_CONTEXTBTNDIR', 0),
            'NOCAPTCHA_SITEKEY' => env('NOCAPTCHA_SITEKEY', ''), 
            'menus' => $request->session()->get('menus'),
            'flash' => [
                'resultado' => $request->session()->get('resultado'),
            ],
            'ziggy' => fn () => array_merge((new Ziggy())->toArray(), [
                'location' => $request->url(),
            ]),        
        ]);        
    }

    private function executarComandoServidor(string $comando, string $comandoAlternativo = null): string
    {
        // Se a função shell_exec estiver desativada no php.ini por segurança
        if (!function_exists('shell_exec')) {
            return 'Não disponível (Função bloqueada)';
        }

        // Executa o primeiro comando direcionando erros
        $retorno = shell_exec($comando . ' 2>&1');

        // Se falhar e houver um comando alternativo (ex: soffice), tenta ele
        if ((empty($retorno) || is_null($retorno)) && $comandoAlternativo) {
            $retorno = shell_exec($comandoAlternativo . ' 2>&1');
        }

        // Tratamento rigoroso anti-quebra
        if (empty($retorno) || is_null($retorno)) {
            return 'Não instalado';
        }

        $retornoBaixo = strtolower($retorno);
        if (str_contains($retornoBaixo, 'not found') || str_contains($retornoBaixo, 'não reconhecido') || str_contains($retornoBaixo, 'is not recognized')) {
            return 'Não instalado';
        }

        // Pega apenas a primeira linha
        $linhas = explode("\n", trim($retorno));
        $textoLimpo = $linhas[0] ?? 'Não instalado';

        // Remove caracteres ocultos de controle e força UTF-8 para não quebrar o JSON do Vue
        $textoLimpo = mb_convert_encoding($textoLimpo, 'UTF-8', 'UTF-8');
        return preg_replace('/[\x00-\x1F\x7F]/', '', $textoLimpo);
    }    
}

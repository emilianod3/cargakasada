<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
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
            ],
            'app_url' => config('app.url'),
            'app_debug' => (bool) env('APP_DEBUG', false),  
            'SISTEMA_CONTEXTBTNDIR' => (bool) env('SISTEMA_CONTEXTBTNDIR', 0), 
            'menus' => $request->session()->get('menus'),   
            'ziggy' => fn () => array_merge((new Ziggy())->toArray(), [
                'location' => $request->url(),
            ]),        
        ]);        
    }
}

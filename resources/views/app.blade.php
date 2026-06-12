<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js'])
        @inertiaHead

        
    </head>
    <body class="font-sans antialiased">
        <noscript>
            <header class="disabledjavascript">
                <strong>Detectamos que seu Browser está com o <a target="_blank" href="https://www.enable-javascript.com/pt/">javascript desabilitado</a></strong>
                <br>Para continuar habilite-o. <a target="_blank" href="https://www.enable-javascript.com/pt/">Veja Como</a>  
            </header>
            <style>#app, footer { display:none !important; }</style>
        </noscript>        
        @inertia
    </body>
</html>

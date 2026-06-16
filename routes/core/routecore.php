<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Core\CfgSistController;
use App\Http\Controllers\Core\UnicoController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
Route::get('/zerar-tudo', function () {
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    \Illuminate\Support\Facades\Artisan::call('route:clear');
    return 'Todos os caches do Laravel foram destruídos com sucesso!';
});*/



/*
Route::group(['prefix' => 'auth', 'middleware' => 'throttle:100,1'], function () {
    Route::post('/autenticar', [AuthController::class, 'autenticar'])->name('autenticar');
    Route::post('/registrar', [AuthController::class, 'registrarusuario'])->name('registrarusuario');
    Route::post('/enviasenharecuperacaoporemail', [AuthController::class, 'enviaSenhaRecuperacaoPorEmail'])->name('enviasenharecuperacaoporemail');
    Route::get('/reset/{token}', [AuthController::class, 'reset']);
});
*/


/*
Route::group(['middleware' => 'throttle:300,1'], function () {
    Route::match(['get', 'post'],'/', function () {
        //$rememberuserlogincheck = (isset($_COOKIE['rememberuserlogincheck']) ? $_COOKIE['rememberuserlogincheck'] : '');
        // Recupera o valor do cache
        $rememberuserlogincheck = Cache::get('rememberuserlogincheck', '');
        return view('auth.login', ['rememberuserlogincheck' => $rememberuserlogincheck]);
    })->name('acesso1');

    Route::get('/login', function () {
        //$rememberuserlogincheck = (isset($_COOKIE['rememberuserlogincheck']) ? $_COOKIE['rememberuserlogincheck'] : '');
        // Recupera o valor do cache
        $rememberuserlogincheck = Cache::get('rememberuserlogincheck', '');        
        return view('auth.login', ['rememberuserlogincheck' => $rememberuserlogincheck]);
    })->name('acesso');

    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/lockscreen', [AuthController::class, 'lockscreen'])->name('lockscreen');
    Route::get('/registro/usuario', function () {
        return view('auth.register');
    })->name('registro.usuario');
    Route::post('/cadastro/novousuario', [UnicoController::class, 'cadastronovousuario'])->name('cadastro.novo.usuario');
    Route::get('/esqueci/senha', function () {
        return view('auth.esquecisenha');
    })->name('esqueci.senha');
    Route::post('/envio/para/recuperarsenha', [UnicoController::class, 'gerartokenrecuperarsenha'])->name('envio.recuperar.senha');
    Route::get('/redefinir/senha/{token}', [UnicoController::class, 'showRecoveryForm'])->where('token', '[a-zA-Z0-9\/\+]+=*')->name('redefinir.senha');
    Route::post('/envio/nova/senha', [UnicoController::class, 'salvarnovasenha'])->name('envio.nova.senha');
});
*/
Route::post('/logatividade', [CfgSistController::class, 'registraAtividade'])->name('logatividade');

Route::middleware(['throttle:100,1'])->group(function () {
    Route::get('/termos', function () {
        return Inertia::render('Ajuda/TermosUsoPublico');
    })->middleware('guest')->name('termos');
    Route::get('/termos/uso', function () {
        return Inertia::render('Ajuda/TermosUso');
    })->middleware(['web','authcheck'])->name('termosuso');
    Route::get('/sobre', function () {
        return Inertia::render('Ajuda/Sobre');
    })->middleware(['web','authcheck'])->name('sobre');       
});


Route::group(['namespace' => 'Cadastro', 'middleware' => 'throttle:100,1', 'prefix' => 'cadastro', 'as' => 'cadastro.'], function () {
    Route::get('/updatetermouso/{iduser}/{tipo}', [UnicoController::class, 'updatetermouso'])->where('iduser', '[0-9]+')->where('tipo', '[0-9]+')->name('updatetermouso')->middleware(['web','authcheck']);    
});
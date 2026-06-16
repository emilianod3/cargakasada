<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Core\UnicoController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'throttle:100,1'], function () {
    Route::post('/autenticar', [AuthController::class, 'autenticar'])->name('autenticar');
    //Route::post('/logout2', [AuthController::class, 'logout'])->middleware(['web'])->name('logout2');
    Route::get('/logout', [AuthController::class, 'logout'])
    ->middleware(['web'])
    ->name('logout'); 

    //Route::post('/registrar', [AuthController::class, 'registrarusuario'])->name('registrarusuario');
    //Route::post('/enviasenharecuperacaoporemail', [AuthController::class, 'enviaSenhaRecuperacaoPorEmail'])->name('enviasenharecuperacaoporemail');
    //Route::get('/reset/{token}', [AuthController::class, 'reset']);

    /*Route::get('/dashboard', [UsuarioController::class, 'index'])->name('dashboard')->middleware('authcheck');
    Route::get('/logout', [UsuarioController::class, 'logout'])->name('logout');
    Route::post('/autenticar', [UsuarioController::class, 'autenticar'])->name('autenticar');
    Route::post('/registrar', [UsuarioController::class, 'registrarusuario'])->name('registrarusuario');
    Route::post('/enviasenharecuperacaoporemail', [UsuarioController::class, 'enviaSenhaRecuperacaoPorEmail'])->name('enviasenharecuperacaoporemail');
    Route::get('/reset/{token}', [UsuarioController::class, 'reset']);
    Route::get('/recuperacaosenha', function () {
        return view('auth.reset');
    })->name('recuperacaosenha');
    Route::post('/reset', [UsuarioController::class, 'updatesenha'])->name('updatesenha');
    Route::get('/userchange/{id}', [UsuarioController::class, 'setUserChange'])->where('id', '[0-9]+')->name('userchange')->middleware('authcheck');*/
});

Route::middleware(['throttle:100,1'])->group(function () {

    // 🟢 ROTA 1: Carrega a página de login (Abre a tela no navegador)
    Route::get('/login', [AuthController::class, 'index'])
    ->middleware(['web', 'redirectlogado'])
    ->name('login');

    Route::get('/lockscreen', [AuthController::class, 'lockscreen'])
    ->middleware(['web'])
    ->name('lockscreen');

    Route::get('/esqueci/senha', [AuthController::class, 'telaesquecisenha'])
    ->middleware(['web', 'redirectlogado'])
    ->name('esquecisenha');

    Route::post('/envio/para/recuperarsenha', [UnicoController::class, 'gerartokenrecuperarsenha'])->name('envio.recuperar.senha');
    Route::get('/redefinir/senha/{token}', [UnicoController::class, 'showRecoveryForm'])->where('token', '[a-zA-Z0-9\/\+]+=*')->name('redefinir.senha');
    Route::post('/envio/nova/senha', [UnicoController::class, 'salvarnovasenha'])->name('envio.nova.senha');    

    /*
    Route::get('/registro/usuario', function () {
        return view('auth.register');
    })->name('registro.usuario');
    Route::post('/cadastro/novousuario', [UnicoController::class, 'cadastronovousuario'])->name('cadastro.novo.usuario');
    Route::get('/esqueci/senha', function () {
        return view('auth.esquecisenha');
    })->name('esqueci.senha');
    
  */  
});







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
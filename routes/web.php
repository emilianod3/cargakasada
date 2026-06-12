<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::middleware(['throttle:100,1'])->group(function () {
    Route::get('/', [AuthController::class, 'index'])
    ->middleware(['web', 'redirectlogado'])
    ->name('inicio');

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
});
/*
Route::get('/', function () {
    return view('welcome');
});
*/

Route::get('/dashboard', function () {
    return Inertia::render('Core/Dashboard');
})->middleware('authcheck')->name('dashboard');


Route::fallback(function() {
    return redirect()->route('login');
})->middleware('throttle:100,1');



Route::get('/exemplocomponentes', function () {
    return Inertia::render('Core/ExemploComponentes');
})->middleware('authcheck')->name('exemplocomponentes');



Route::get('/teste', function () {
    return Inertia::render('Home', [
        'nome' => 'Cauan'
    ]);
});

Route::get('/teste-vue', function () {
    return Inertia::render('TesteVue');
});
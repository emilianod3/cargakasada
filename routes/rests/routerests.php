<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Core\CfgSistController;
use App\Http\Controllers\Core\UnicoController;
use App\Http\Controllers\Rest\ContatoController;
use App\Http\Controllers\Rest\ProblemaController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
    Route::get('/contato', function () {
        return Inertia::render('Ajuda/Contato');
    })->middleware(['web','authcheck'])->name('contato');        
});


Route::group(['namespace' => 'Cadastro', 'middleware' => 'throttle:100,1', 'prefix' => 'cadastro', 'as' => 'cadastro.'], function () {
    Route::get('/updatetermouso/{iduser}/{tipo}', [UnicoController::class, 'updatetermouso'])->where('iduser', '[0-9]+')->where('tipo', '[0-9]+')->name('updatetermouso')->middleware(['web','authcheck']);    
});

/*
Route::group(['prefix' => 'problema', 'namespace' => 'Rest', 'middleware' => 'throttle:40,1'], function () {
    Route::post('/reportarproblema', [ProblemaController::class, 'reporteproblema'])->name('reportar');
});*/

Route::group(['prefix' => 'contato', 'namespace' => 'Rest', 'middleware' => 'throttle:40,1', 'as' => 'rest.'], function () {
    Route::post('/formulariocontato', [ContatoController::class, 'formulariocontato'])->name('formulariocontato');
});

Route::group(['prefix' => 'problema', 'namespace' => 'Rest', 'middleware' => 'throttle:40,1'], function () {
    Route::post('/reportarproblema', [ProblemaController::class, 'reporteproblema'])->name('reportarproblema');
});
<?php

use App\Http\Controllers\Controle\CalController;
use App\Http\Controllers\Core\MenusController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::group(['prefix' => 'controle', 'namespace' => 'Controle', 'middleware' => 'throttle:300,1', 'as' => 'controle.'], function () {
    Route::get('/cals', [CalController::class, 'inicio'])->name('cals')->middleware(['web','authcheck']);
    Route::post('/calslista', [CalController::class, 'lista'])->name('cals.lista')->middleware(['web','authcheck']);
    Route::get('/calsget/{id}', [CalController::class, 'get'])->where('id', '[0-9]+')->name('cals.get')->middleware(['web','authcheck']);
    Route::post('/calssalvar', [CalController::class, 'salvar'])->name('cals.salvar')->middleware(['web','authcheck']);
    Route::post('/calupdate', [CalController::class, 'update'])->name('cals.update')->middleware(['web','authcheck']);
    Route::get('/calsremover/{id}', [CalController::class, 'removerId'])->where('id', '[0-9]+')->name('cals.remover')->middleware(['web','authcheck']);
    Route::get('/calsgetall', [CalController::class, 'getall'])->name('cals.getall')->middleware(['web','authcheck']);
    Route::get('/calsgetallobj', [CalController::class, 'getAllObj'])->name('cals.getallobj')->middleware(['web','authcheck']);
    Route::post('/calrelatorio', [CalController::class, 'relatorio'])->name('cals.relatorio')->middleware(['web','authcheck']);
});

Route::group(['prefix' => 'controle', 'namespace' => 'Controle', 'middleware' => 'throttle:300,1', 'as' => 'controle.'], function () {
    Route::get('/menus', [MenusController::class, 'inicio'])->name('menus')->middleware('authcheck');
    Route::post('/menuslista', [MenusController::class, 'lista'])->name('menus.lista')->middleware('authcheck');
    Route::get('/menusget/{id}', [MenusController::class, 'get'])->where('id', '[0-9]+')->name('menus.get')->middleware('authcheck');
    Route::post('/menussalvar', [MenusController::class, 'salvar'])->name('menus.salvar')->middleware('authcheck');
    Route::get('/menusremover/{id}', [MenusController::class, 'removerId'])->where('id', '[0-9]+')->name('menus.remover')->middleware('authcheck');
    Route::get('/menusgetall', [MenusController::class, 'getall'])->name('menus.getall')->middleware('authcheck');
    Route::post('/menuupdate', [MenusController::class, 'update'])->name('menus.update')->middleware(['web','authcheck']);
    Route::post('/menurelatorio', [MenusController::class, 'relatorio'])->name('menus.relatorio')->middleware(['web','authcheck']);
});

<?php

use App\Http\Controllers\Controle\CalController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;



Route::group(['prefix' => 'controle', 'namespace' => 'Controle', 'middleware' => 'throttle:300,1', 'as' => 'controle.'], function () {
    Route::get('/cals', [CalController::class, 'inicio'])->name('cals')->middleware(['web','authcheck']);
    Route::post('/calslista', [CalController::class, 'lista'])->name('cals.lista')->middleware(['web','authcheck']);
    Route::get('/calsget/{id}', [CalController::class, 'get'])->where('id', '[0-9]+')->name('cals.get')->middleware(['web','authcheck']);
    Route::post('/calssalvar', [CalController::class, 'salvar'])->name('cals.salvar')->middleware(['web','authcheck']);
    Route::get('/calsremover/{id}', [CalController::class, 'removerId'])->where('id', '[0-9]+')->name('cals.remover')->middleware(['web','authcheck']);
    Route::get('/calsgetall', [CalController::class, 'getall'])->name('cals.getall')->middleware(['web','authcheck']);
    Route::get('/calsgetallobj', [CalController::class, 'getAllObj'])->name('cals.getallobj')->middleware(['web','authcheck']);
});

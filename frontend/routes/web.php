<?php

use App\Http\Controllers\StockController;
use App\Http\Controllers\CuradoriaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::get('/analyze/{symbol}', [StockController::class, 'analyze']);

Route::get('/curadoria', [CuradoriaController::class, 'index'])->name('curadoria.index');
Route::get('/curadoria/{id}', [CuradoriaController::class, 'show'])->name('curadoria.show');

// Rota de UPDATE Focada no Status (o 'U' do CRUD)
Route::put('/curadoria/status/{id}', [CuradoriaController::class, 'updateStatus'])->name('curadoria.update_status');

// Rota de DELETE
Route::delete('/curadoria/{id}', [CuradoriaController::class, 'destroy'])->name('curadoria.destroy');

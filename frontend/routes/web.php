<?php

use App\Http\Controllers\StockController;
use App\Http\Controllers\CuradoriaController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// ===== ROTAS DE AUTENTICAÇÃO (Públicas) =====
Route::get('/login', [AuthController::class, 'showLogin'])->name('auth.showLogin')->middleware('guest.auth');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login')->middleware('guest.auth');

Route::get('/register', [AuthController::class, 'showRegister'])->name('auth.showRegister')->middleware('guest.auth');
Route::post('/register', [AuthController::class, 'register'])->name('auth.register')->middleware('guest.auth');

// ===== ROTAS PROTEGIDAS (Requerem Autenticação) =====
Route::middleware('auth.user')->group(function () {
    Route::get('/', function () {
        return view('index');
    })->name('index');

    Route::get('/analyze/{symbol}', [StockController::class, 'analyze']);

    Route::get('/curadoria', [CuradoriaController::class, 'index'])->name('curadoria.index');
    Route::get('/curadoria/{id}', [CuradoriaController::class, 'show'])->name('curadoria.show');

    // Rota de UPDATE Focada no Status (o 'U' do CRUD)
    Route::put('/curadoria/status/{id}', [CuradoriaController::class, 'updateStatus'])->name('curadoria.update_status');

    // Rota de DELETE
    Route::delete('/curadoria/{id}', [CuradoriaController::class, 'destroy'])->name('curadoria.destroy');

    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
});

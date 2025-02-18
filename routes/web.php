<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::prefix('users')->group(function () {
        Route::get('/', [UsersController::class, 'index'])->name('users.index'); // Listar usuários
        Route::get('/create', [UsersController::class, 'create'])->name('users.create'); // Formulário de criação
        Route::post('/', [UsersController::class, 'store'])->name('users.store'); // Salvar novo usuário
        Route::get('/{id}/edit', [UsersController::class, 'edit'])->name('users.edit'); // Formulário de edição
        Route::put('/{id}', [UsersController::class, 'update'])->name('users.update'); // Atualizar usuário
        Route::delete('/{id}', [UsersController::class, 'destroy'])->name('users.destroy'); // Excluir usuário
    });


    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

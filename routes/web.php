<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\CollaboratorsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::prefix('users')->group(function () {
        Route::get('/', [UsersController::class, 'index'])->name('users.index')->middleware('permission:Lista de usuários'); // Listar usuários
        Route::get('/create', [UsersController::class, 'create'])->name('users.create')->middleware('permission:Formulário de criação dos usuários'); // Formulário de criação
        Route::post('/', [UsersController::class, 'store'])->name('users.store')->middleware('permission:Salvar usuários'); // Salvar novo usuário
        Route::get('/{id}/edit', [UsersController::class, 'edit'])->name('users.edit')->middleware('permission:Formulário de edição dos usuários'); // Formulário de edição
        Route::put('/{id}', [UsersController::class, 'update'])->name('users.update')->middleware('permission:Atualizar usuários'); // Atualizar usuário
        Route::delete('/{id}', [UsersController::class, 'destroy'])->name('users.destroy')->middleware('permission:Deletar usuários'); // Excluir usuário
    });

    Route::prefix('collaborators')->group(function () {
        Route::get('/', [CollaboratorsController::class, 'index'])->name('collaborators.index')->middleware('permission:Lista de colaboradores');
        Route::get('/create', [CollaboratorsController::class, 'create'])->name('collaborators.create')->middleware('permission:Formulário de criação dos colaboradores');
        Route::post('/', [CollaboratorsController::class, 'store'])->name('collaborators.store')->middleware('permission:Salvar colaboradores');
        Route::get('/{id}/edit', [CollaboratorsController::class, 'edit'])->name('collaborators.edit')->middleware('permission:Formulário de edição dos colaboradores');
        Route::put('/{id}', [CollaboratorsController::class, 'update'])->name('collaborators.update')->middleware('permission:Atualizar colaboradores');
        Route::delete('/{id}', [CollaboratorsController::class, 'destroy'])->name('collaborators.destroy')->middleware('permission:Deletar colaboradores');
    });


    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

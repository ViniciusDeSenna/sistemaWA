<?php

use App\Http\Controllers\DailyRateController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\CollaboratorsController;
use App\Http\Controllers\CompanyController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::prefix('users')->group(function () {
        Route::get('/', [UsersController::class, 'index'])->name('users.index')->middleware('permission:Lista de usuários'); // Listar usuários
        Route::get('/table', [UsersController::class, 'table'])->name('users.table')->middleware('permission:Lista de usuários');
        Route::get('/create', [UsersController::class, 'create'])->name('users.create')->middleware('permission:Formulário de criação dos usuários'); // Formulário de criação
        Route::post('/', [UsersController::class, 'store'])->name('users.store')->middleware('permission:Salvar usuários'); // Salvar novo usuário
        Route::get('/{id}/edit', [UsersController::class, 'edit'])->name('users.edit')->middleware('permission:Formulário de edição dos usuários'); // Formulário de edição
        Route::put('/{id}', [UsersController::class, 'update'])->name('users.update')->middleware('permission:Atualizar usuários'); // Atualizar usuário
        Route::delete('/{id}', [UsersController::class, 'destroy'])->name('users.destroy')->middleware('permission:Deletar usuários'); // Excluir usuário
    });

    Route::prefix('collaborators')->group(function () {
        Route::get('/', [CollaboratorsController::class, 'index'])->name('collaborators.index')->middleware('permission:Lista de colaboradores');
        Route::get('/table', [CollaboratorsController::class, 'table'])->name('collaborators.table')->middleware('permission:Lista de colaboradores');
        Route::get('/create', [CollaboratorsController::class, 'create'])->name('collaborators.create')->middleware('permission:Formulário de criação dos colaboradores');
        Route::post('/', [CollaboratorsController::class, 'store'])->name('collaborators.store')->middleware('permission:Salvar colaboradores');
        Route::get('/{id}/edit', [CollaboratorsController::class, 'edit'])->name('collaborators.edit')->middleware('permission:Formulário de edição dos colaboradores');
        Route::put('/{id}', [CollaboratorsController::class, 'update'])->name('collaborators.update')->middleware('permission:Atualizar colaboradores');
        Route::delete('/{id}', [CollaboratorsController::class, 'destroy'])->name('collaborators.destroy')->middleware('permission:Deletar colaboradores');
    });

    Route::prefix('companies')->group(function () {
        Route::get('/', [CompanyController::class, 'index'])->name('companies.index')->middleware('permission:Lista de estabelecimentos');
        Route::get('/table', [CompanyController::class, 'table'])->name('companies.table')->middleware('permission:Lista de estabelecimentos');
        Route::get('/create', [CompanyController::class, 'create'])->name('companies.create')->middleware('permission:Formulário de criação dos estabelecimentos');
        Route::post('/', [CompanyController::class, 'store'])->name('companies.store')->middleware('permission:Salvar estabelecimentos');
        Route::get('/{id}/edit', [CompanyController::class, 'edit'])->name('companies.edit')->middleware('permission:Formulário de edição dos estabelecimentos');
        Route::put('/{id}', [CompanyController::class, 'update'])->name('companies.update')->middleware('permission:Atualizar estabelecimentos');
        Route::delete('/{id}', [CompanyController::class, 'destroy'])->name('companies.destroy')->middleware('permission:Deletar estabelecimentos');

        Route::get('/hourly-rate/{id}', [CompanyController::class, 'getHourlyRate'])->name('companies.hourly-rate');
    });

    Route::prefix('daily-rate')->group(function () {
        Route::get('/', [DailyRateController::class, 'index'])->name('daily-rate.index')->middleware('permission:Lista de diárias');
        Route::get('/table', [DailyRateController::class, 'table'])->name('daily-rate.table')->middleware('permission:Lista de diárias');
        Route::get('/makepdf', [DailyRateController::class, 'makePDF'])->name('daily-rate.makepdf')->middleware('permission:Lista de diárias');
        Route::get('/create', [DailyRateController::class, 'create'])->name('daily-rate.create')->middleware('permission:Formulário de criação dos diárias');
        Route::post('/', [DailyRateController::class, 'store'])->name('daily-rate.store')->middleware('permission:Salvar diárias');
        Route::get('/{id}/edit', [DailyRateController::class, 'edit'])->name('daily-rate.edit')->middleware('permission:Formulário de edição dos diárias');
        Route::put('/{id}', [DailyRateController::class, 'update'])->name('daily-rate.update')->middleware('permission:Atualizar diárias');
        Route::delete('/{id}', [DailyRateController::class, 'destroy'])->name('daily-rate.destroy')->middleware('permission:Deletar diárias');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

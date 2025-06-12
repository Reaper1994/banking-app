<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SavingsAccountController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/settings/two-factor-authentication', function () {
        return Inertia::render('settings/two-factor-authentication');
    })->name('two-factor-authentication');

    // Savings Accounts Routes
    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::get('/savings-accounts', [SavingsAccountController::class, 'index'])->name('savings-accounts.index');
        Route::get('/savings-accounts/create', [SavingsAccountController::class, 'create'])->name('savings-accounts.create');
        Route::post('/savings-accounts', [SavingsAccountController::class, 'store'])->name('savings-accounts.store');
        Route::get('/savings-accounts/{savings_account}', [SavingsAccountController::class, 'show'])->name('savings-accounts.show');
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

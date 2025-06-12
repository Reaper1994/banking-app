<?php

use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticationController;

Route::middleware('auth')->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('settings/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('settings/password', [PasswordController::class, 'edit'])->name('password.edit');
    Route::put('settings/password', [PasswordController::class, 'update'])->name('password.update');

    Route::get('settings/appearance', function () {
        return Inertia::render('settings/appearance');
    })->name('appearance');

    Route::get('settings/two-factor-authentication', function () {
        return Inertia::render('settings/two-factor-authentication');
    })->name('two-factor-authentication');

    Route::post('user/two-factor-authentication', [TwoFactorAuthenticationController::class, 'store'])
        ->middleware(['auth', 'password.confirm'])
        ->name('two-factor.enable');

    Route::post('user/two-factor-authentication/confirm', [TwoFactorAuthenticationController::class, 'confirm'])
        ->middleware(['auth', 'password.confirm'])
        ->name('two-factor.confirm');

    Route::delete('user/two-factor-authentication', [TwoFactorAuthenticationController::class, 'destroy'])
        ->middleware(['auth', 'password.confirm'])
        ->name('two-factor.disable');
});

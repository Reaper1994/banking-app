<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticationController;

Route::group(['middleware' => config('fortify.middleware', ['web'])], function () {

    if (Features::enabled(Features::twoFactorAuthentication())) {
        Route::get('/user/two-factor-authentication', [TwoFactorAuthenticationController::class, 'show'])
            ->middleware(['auth', 'password.confirm'])
            ->name('two-factor.show');

        Route::post('/user/two-factor-authentication', [TwoFactorAuthenticationController::class, 'store'])
            ->middleware(['auth', 'password.confirm'])
            ->name('two-factor.enable');

        Route::post('/user/two-factor-authentication/confirm', [TwoFactorAuthenticationController::class, 'confirm'])
            ->middleware(['auth', 'password.confirm'])
            ->name('two-factor.confirm');

        Route::delete('/user/two-factor-authentication', [TwoFactorAuthenticationController::class, 'destroy'])
            ->middleware(['auth', 'password.confirm'])
            ->name('two-factor.disable');
    }
});

<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\TransferController;
use Illuminate\Support\Facades\Route;

//TODO: Add middleware for authentication and authorization
Route::prefix('v1')
    ->middleware(['api', 'throttle:60,1']) // 60 requests per minute
    ->group(function () {
        Route::post('/transfers', [TransferController::class, 'transfer'])
            ->middleware('throttle:10,1'); // 10 transfers per minute
    });

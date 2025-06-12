<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\TransferController;
use Illuminate\Support\Facades\Route;

//TODO: Add middleware for authentication and authorization
Route::prefix('v1')->middleware([])->group(function () {
    Route::post('/transfers', [TransferController::class, 'transfer']);
}); 
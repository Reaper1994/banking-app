<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\Interfaces\TransferRepositoryInterface;
use App\Repositories\TransferRepository;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TransferRepositoryInterface::class, TransferRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\CurrencyConversionService;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;

final class CurrencyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CurrencyConversionService::class, function ($app) {
            $apiKey = config('services.exchange_rates.api_key');
            
            if (empty($apiKey)) {
                throw new InvalidArgumentException(
                    'Exchange Rates API key is not set. Please add EXCHANGE_RATES_API_KEY to your .env file.'
                );
            }

            return new CurrencyConversionService($apiKey);
        });
    }
} 
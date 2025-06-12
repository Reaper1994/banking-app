<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;

final class CurrencyConversionService
{
    //TODO::moved to a config file
    private const API_URL = 'http://api.exchangeratesapi.io/v1/latest';
    private const CACHE_TTL = 120; // 2 min
    private const SPREAD = 0.01; // 1% spread

    public function __construct(
        private readonly string $apiKey
    ) {
    }

    public function convert(float $amount, string $fromCurrency, string $toCurrency): float
    {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        $rates = $this->getExchangeRates($fromCurrency);
        
        if (!isset($rates[$toCurrency])) {
            throw new InvalidArgumentException("Unsupported currency: {$toCurrency}");
        }

        $rate = $rates[$toCurrency];
        $convertedAmount = $amount * $rate;
        
        return $convertedAmount * (1 - self::SPREAD);
    }

    private function getExchangeRates(string $baseCurrency): array
    {
        return Cache::remember(
            "exchange_rates_{$baseCurrency}",
            self::CACHE_TTL,
            function () use ($baseCurrency) {

                //NOTE:free subscriotin only supports base currency as usd ,i cant request for a different base currency.
                $response = Http::get(self::API_URL, [
                    'access_key' => $this->apiKey,
                    'symbols' => 'USD,EUR,GBP',
                    'format' => 1,
                ]);
             

                if (!$response->successful()) {
                    throw new InvalidArgumentException('Failed to fetch exchange rates');
                }

                $data = $response->json();
             
                if (!isset($data['rates'][$baseCurrency])) {
                    throw new InvalidArgumentException("Exchange rate for {$baseCurrency} not found");
                }

                return $data['rates'];
            }
        );
    }
} 
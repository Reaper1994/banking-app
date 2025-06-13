<?php

namespace App\Providers;

use App\Models\SavingsAccount;
use App\Policies\SavingsAccountPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        SavingsAccount::class => SavingsAccountPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}

<?php

namespace App\Policies;

use App\Models\SavingsAccount;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SavingsAccountPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function view(User $user, SavingsAccount $savingsAccount): bool
    {
        return $user->hasRole('admin') || $user->id === $savingsAccount->user_id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, SavingsAccount $savingsAccount): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, SavingsAccount $savingsAccount): bool
    {
        return $user->hasRole('admin');
    }
}

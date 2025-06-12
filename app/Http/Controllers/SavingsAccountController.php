<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSavingsAccountsRequest;
use App\Models\SavingsAccount;
use App\Models\User;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class SavingsAccountController extends Controller
{
    public function __construct()
    {
        Gate::authorize('viewAny', SavingsAccount::class);
    }

    public function index()
    {
        $accounts = SavingsAccount::with(['user:id,email,first_name,last_name,address', 'currency'])
            ->latest()
            ->paginate(10);

        return Inertia::render('SavingsAccounts/Index', [
            'accounts' => $accounts,
        ]);
    }

    public function create()
    {
        Gate::authorize('create', SavingsAccount::class);

        $users = User::role('client')
            ->select(['id', 'name', 'email', 'first_name', 'last_name', 'date_of_birth', 'address'])
            ->get();

        $currencies = Currency::where('is_active', true)->get(['id', 'code', 'name', 'symbol']);

        return Inertia::render('SavingsAccounts/Create', [
            'users' => $users,
            'currencies' => $currencies,
        ]);
    }

    public function store(StoreSavingsAccountsRequest $request)
    {
        Gate::authorize('create', SavingsAccount::class);

        DB::beginTransaction();

        try {
            foreach ($request->accounts as $accountData) {
                // Get the selected user
                $user = User::findOrFail($accountData['user_id']);

                $currency = Currency::findOrFail($accountData['currency_id']);
                $initialBalance = config('savings.initial_balance') * $currency->exchange_rate;

                $user = User::findOrFail($user->id);
                
                $user->update([
                    'first_name' => $accountData['first_name'],
                    'last_name' => $accountData['last_name'],
                    'date_of_birth' => $accountData['date_of_birth'],
                    'address' => $accountData['address'],
                ]);

                SavingsAccount::create([
                    'user_id' => $user->id,
                    'balance' => $initialBalance,
                    'currency_id' => $accountData['currency_id'],
                    'is_active' => true,
                ]);
            }

            DB::commit();

            return redirect()->route('savings-accounts.index')
                ->with('success', 'Savings accounts created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

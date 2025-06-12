<?php

namespace App\Http\Controllers;

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
        $accounts = SavingsAccount::with(['user', 'currency'])
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

    public function store(Request $request)
    {
        Gate::authorize('create', SavingsAccount::class);

        $request->validate([
            'accounts' => 'required|array|min:1',
            'accounts.*.user_id' => 'required|exists:users,id',
            'accounts.*.first_name' => 'required|string|max:255',
            'accounts.*.last_name' => 'required|string|max:255',
            'accounts.*.date_of_birth' => 'required|date',
            'accounts.*.address' => 'required|string|max:255',
            'accounts.*.currency_id' => 'required|exists:currencies,id',
        ]);

        DB::beginTransaction();

        try {
            foreach ($request->accounts as $accountData) {
                // Get the selected user
                $user = User::findOrFail($accountData['user_id']);

                // Get currency exchange rate
                $currency = Currency::findOrFail($accountData['currency_id']);
                $initialBalance = 10000 * $currency->exchange_rate;

                // Create savings account
                SavingsAccount::create([
                    'account_number' => 'SAV' . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT),
                    'user_id' => $user->id,
                    'first_name' => $accountData['first_name'],
                    'last_name' => $accountData['last_name'],
                    'date_of_birth' => $accountData['date_of_birth'],
                    'address' => $accountData['address'],
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

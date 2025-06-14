<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreSavingsAccountsRequest;
use App\Models\Currency;
use App\Models\SavingsAccount;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class SavingsAccountController extends Controller
{
    public function __construct()
    {
        Gate::authorize('viewAny', SavingsAccount::class);
    }

    /**
     * Display a paginated list of savings accounts.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $query = SavingsAccount::with(['user:id,email,first_name,last_name,address', 'currency']);

        if ($request->filled('account_number')) {
            $query->where('account_number', 'like', '%' . $request->string('account_number') . '%');
        }

        if ($request->filled('name')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->string('name') . '%')
                  ->orWhere('last_name', 'like', '%' . $request->string('name') . '%');
            });
        }

        if ($request->filled('email')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('email', 'like', '%' . $request->string('email') . '%');
            });
        }


        if ($request->filled('min_balance')) {
            $query->where('balance', '>=', $request->float('min_balance'));
        }
        if ($request->filled('max_balance')) {
            $query->where('balance', '<=', $request->float('max_balance'));
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->string('status') === 'active');
        }

        if ($request->filled('currency_id')) {
            $query->where('currency_id', $request->integer('currency_id'));
        }

        $accounts = $query->latest()->paginate(10)->withQueryString();

        $currencies = Currency::where('is_active', true)->get(['id', 'code', 'name', 'symbol']);

        return Inertia::render('SavingsAccounts/Index', [
            'accounts' => $accounts,
            'currencies' => $currencies,
            'filters' => $request->only([
                'account_number',
                'name',
                'email',
                'min_balance',
                'max_balance',
                'status',
                'currency_id',
            ]),
        ]);
    }

    /**
     * Show the form for creating new savings accounts.
     *
     * @return Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create(): Response
    {
        Gate::authorize('create', SavingsAccount::class);

        $users = User::role('client')
            ->select(['id', 'name', 'email', 'first_name', 'last_name', 'date_of_birth', 'address'])
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'date_of_birth' => $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : null,
                    'address' => $user->address,
                ];
            });

        $currencies = Currency::where('is_active', true)->get(['id', 'code', 'name', 'symbol']);

        return Inertia::render('SavingsAccounts/Create', [
            'users' => $users,
            'currencies' => $currencies,
        ]);
    }

    /**
     * Store newly created savings accounts in the database.
     *
     * @param StoreSavingsAccountsRequest $request
     * @return RedirectResponse
     * @throws \Throwable
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreSavingsAccountsRequest $request): RedirectResponse
    {
        Gate::authorize('create', SavingsAccount::class);

        DB::beginTransaction();

        try {
            foreach ($request->accounts as $accountData) {
                $user = User::findOrFail($accountData['user_id']);
                $currency = Currency::findOrFail($accountData['currency_id']);
                $initialBalance = config('savings.initial_balance');

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

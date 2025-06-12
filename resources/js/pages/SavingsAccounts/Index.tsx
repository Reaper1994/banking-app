import { Head, Link, router } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { useState } from 'react';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { ChevronLeft, ChevronRight } from 'lucide-react';

interface Currency {
    id: number;
    code: string;
    name: string;
    symbol: string;
}

interface SavingsAccount {
    id: number;
    account_number: string;
    balance: string | number;
    is_active: boolean;
    user: {
        email: string;
        address: string;
        first_name: string;
        last_name: string;
    };
    currency: Currency;
}

interface Props {
    accounts: {
        data: SavingsAccount[];
        links: any[];
    };
    currencies: Currency[];
    filters: {
        account_number?: string;
        name?: string;
        email?: string;
        min_balance?: string;
        max_balance?: string;
        status?: string;
        currency_id?: string;
    };
}

interface SearchParams {
    account_number: string;
    name: string;
    email: string;
    min_balance: string;
    max_balance: string;
    status: string;
    currency_id: string;
}

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Savings Accounts', href: '/savings-accounts' },
];

export default function Index({ accounts, currencies, filters }: Props) {
    const [searchParams, setSearchParams] = useState<SearchParams>({
        account_number: filters.account_number || '',
        name: filters.name || '',
        email: filters.email || '',
        min_balance: filters.min_balance || '',
        max_balance: filters.max_balance || '',
        status: filters.status || 'all',
        currency_id: filters.currency_id || 'all',
    });

    const handleSearch = () => {
        const params: Partial<SearchParams> = { ...searchParams };
        if (params.status === 'all') delete params.status;
        if (params.currency_id === 'all') delete params.currency_id;
        
        router.get(route('savings-accounts.index'), params, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handleReset = () => {
        setSearchParams({
            account_number: '',
            name: '',
            email: '',
            min_balance: '',
            max_balance: '',
            status: 'all',
            currency_id: 'all',
        });
        router.get(route('savings-accounts.index'));
    };

    const formatBalance = (balance: string | number, currency: Currency) => {
        const numericBalance = typeof balance === 'string' ? parseFloat(balance) : balance;
        return `${currency.symbol}${numericBalance.toFixed(2)}`;
    };

    const renderPaginationLink = (link: any, index: number) => {
        if (link.label === '&laquo; Previous') {
            return (
                <Link
                    key={index}
                    href={link.url || '#'}
                    className={`relative inline-flex items-center px-4 py-2 text-sm font-medium rounded-md ${
                        !link.url
                            ? 'text-gray-400 cursor-not-allowed'
                            : 'text-gray-700 hover:bg-gray-50'
                    } border`}
                >
                    <ChevronLeft className="h-4 w-4 mr-1" />
                    Previous
                </Link>
            );
        }
        if (link.label === 'Next &raquo;') {
            return (
                <Link
                    key={index}
                    href={link.url || '#'}
                    className={`relative inline-flex items-center px-4 py-2 text-sm font-medium rounded-md ${
                        !link.url
                            ? 'text-gray-400 cursor-not-allowed'
                            : 'text-gray-700 hover:bg-gray-50'
                    } border`}
                >
                    Next
                    <ChevronRight className="h-4 w-4 ml-1" />
                </Link>
            );
        }
        return (
            <Link
                key={index}
                href={link.url || '#'}
                className={`relative inline-flex items-center px-4 py-2 text-sm font-medium rounded-md ${
                    link.active
                        ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                        : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                } border`}
            >
                {link.label}
            </Link>
        );
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Savings Accounts" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="flex justify-between items-center mb-6">
                        <h2 className="text-2xl font-semibold">Savings Accounts</h2>
                        <Link href={route('savings-accounts.create')}>
                            <Button>Create New Account</Button>
                        </Link>
                    </div>

                    <Card className="mb-6">
                        <CardHeader>
                            <CardTitle>Search Filters</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div>
                                    <label className="block text-sm font-medium mb-1">Account Number</label>
                                    <Input
                                        type="text"
                                        value={searchParams.account_number}
                                        onChange={(e) => setSearchParams({ ...searchParams, account_number: e.target.value })}
                                        placeholder="Search by account number"
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium mb-1">Name</label>
                                    <Input
                                        type="text"
                                        value={searchParams.name}
                                        onChange={(e) => setSearchParams({ ...searchParams, name: e.target.value })}
                                        placeholder="Search by name"
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium mb-1">Email</label>
                                    <Input
                                        type="email"
                                        value={searchParams.email}
                                        onChange={(e) => setSearchParams({ ...searchParams, email: e.target.value })}
                                        placeholder="Search by email"
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium mb-1">Min Balance</label>
                                    <Input
                                        type="number"
                                        value={searchParams.min_balance}
                                        onChange={(e) => setSearchParams({ ...searchParams, min_balance: e.target.value })}
                                        placeholder="Minimum balance"
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium mb-1">Max Balance</label>
                                    <Input
                                        type="number"
                                        value={searchParams.max_balance}
                                        onChange={(e) => setSearchParams({ ...searchParams, max_balance: e.target.value })}
                                        placeholder="Maximum balance"
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium mb-1">Status</label>
                                    <Select
                                        value={searchParams.status}
                                        onValueChange={(value) => setSearchParams({ ...searchParams, status: value })}
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select status" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="all">All</SelectItem>
                                            <SelectItem value="active">Active</SelectItem>
                                            <SelectItem value="inactive">Inactive</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium mb-1">Currency</label>
                                    <Select
                                        value={searchParams.currency_id}
                                        onValueChange={(value) => setSearchParams({ ...searchParams, currency_id: value })}
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select currency" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="all">All</SelectItem>
                                            {currencies.map((currency) => (
                                                <SelectItem key={currency.id} value={currency.id.toString()}>
                                                    {currency.code}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                </div>
                            </div>
                            <div className="flex justify-end space-x-4 mt-4">
                                <Button variant="outline" onClick={handleReset}>
                                    Reset
                                </Button>
                                <Button onClick={handleSearch}>
                                    Search
                                </Button>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>All Savings Accounts</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="overflow-x-auto">
                                <table className="min-w-full divide-y divide-gray-200">
                                    <thead className="bg-gray-50">
                                        <tr>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Account Number
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Name
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Email
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Address
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Balance
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Currency
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Status
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody className="bg-white divide-y divide-gray-200">
                                        {accounts.data.map((account) => (
                                            <tr key={account.id}>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {account.account_number}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {account.user.first_name} {account.user.last_name}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {account.user.email}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {account.user.address}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {formatBalance(account.balance, account.currency)}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {account.currency.code}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <span className={`px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${
                                                        account.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                                                    }`}>
                                                        {account.is_active ? 'Active' : 'Inactive'}
                                                    </span>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>

                            <div className="mt-4">
                                <nav className="flex items-center justify-between">
                                    <div className="flex-1 flex justify-between sm:hidden">
                                        {accounts.links.map((link, i) => renderPaginationLink(link, i))}
                                    </div>
                                    <div className="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                        <div>
                                            <p className="text-sm text-gray-700">
                                                Showing <span className="font-medium">{accounts.data.length}</span> results
                                            </p>
                                        </div>
                                        <div>
                                            <nav className="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                                {accounts.links.map((link, i) => renderPaginationLink(link, i))}
                                            </nav>
                                        </div>
                                    </div>
                                </nav>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
}

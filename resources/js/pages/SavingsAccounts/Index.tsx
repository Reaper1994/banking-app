import { Head } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Link } from '@inertiajs/react';
import { ChevronLeft, ChevronRight } from 'lucide-react';

interface Currency {
    id: number;
    code: string;
    symbol: string;
}

interface SavingsAccount {
    id: number;
    account_number: string;
    first_name: string;
    last_name: string;
    balance: string | number;
    is_active: boolean;
    user: {
        email: string;
    };
    currency: Currency;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedData {
    data: SavingsAccount[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
    links: PaginationLink[];
}

interface Props {
    accounts: PaginatedData;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Savings Accounts',
        href: '/savings-accounts',
    },
];

export default function Index({ accounts }: Props) {
    const formatBalance = (balance: string | number, currency: Currency) => {
        const numericBalance = typeof balance === 'string' ? parseFloat(balance) : balance;
        return `${currency.symbol}${numericBalance.toFixed(2)}`;
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
                                                    {account.first_name} {account.last_name}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {account.user.email}
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

                            {/* Pagination */}
                            <div className="mt-6 flex items-center justify-between">
                                <div className="text-sm text-gray-700">
                                    Showing <span className="font-medium">{accounts.from}</span> to{' '}
                                    <span className="font-medium">{accounts.to}</span> of{' '}
                                    <span className="font-medium">{accounts.total}</span> results
                                </div>
                                <div className="flex items-center space-x-2">
                                    {accounts.links.map((link, i) => {
                                        if (i === 0) {
                                            return (
                                                <Link
                                                    key={i}
                                                    href={link.url || '#'}
                                                    className={`inline-flex items-center px-3 py-1 rounded-md text-sm font-medium ${
                                                        !link.url
                                                            ? 'text-gray-400 cursor-not-allowed'
                                                            : 'text-gray-700 hover:bg-gray-50'
                                                    }`}
                                                >
                                                    <ChevronLeft className="h-4 w-4" />
                                                </Link>
                                            );
                                        }
                                        if (i === accounts.links.length - 1) {
                                            return (
                                                <Link
                                                    key={i}
                                                    href={link.url || '#'}
                                                    className={`inline-flex items-center px-3 py-1 rounded-md text-sm font-medium ${
                                                        !link.url
                                                            ? 'text-gray-400 cursor-not-allowed'
                                                            : 'text-gray-700 hover:bg-gray-50'
                                                    }`}
                                                >
                                                    <ChevronRight className="h-4 w-4" />
                                                </Link>
                                            );
                                        }
                                        return (
                                            <Link
                                                key={i}
                                                href={link.url || '#'}
                                                className={`inline-flex items-center px-3 py-1 rounded-md text-sm font-medium ${
                                                    link.active
                                                        ? 'bg-indigo-600 text-white'
                                                        : 'text-gray-700 hover:bg-gray-50'
                                                }`}
                                            >
                                                {link.label}
                                            </Link>
                                        );
                                    })}
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
}

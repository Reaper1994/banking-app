import { useState } from 'react';
import { Head, useForm } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';

interface User {
    id: number;
    name: string;
    email: string;
    first_name?: string;
    last_name?: string;
    date_of_birth?: string;
    address?: string;
}

interface Currency {
    id: number;
    code: string;
    name: string;
    symbol: string;
}

interface AccountFormData {
    user_id: string;
    first_name: string;
    last_name: string;
    date_of_birth: string;
    address: string;
    currency_id: string;
}

interface FormData {
    accounts: AccountFormData[];
    [key: string]: any;
}

interface Props {
    users: User[];
    currencies: Currency[];
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
    {
        title: 'Create',
        href: '/savings-accounts/create',
    },
];

export default function Create({ users, currencies }: Props) {
    const [accounts, setAccounts] = useState([{ id: 1 }]);

    const { data, setData, post, processing, errors } = useForm<FormData>({
        accounts: [{
            user_id: '',
            first_name: '',
            last_name: '',
            date_of_birth: '',
            address: '',
            currency_id: '',
        }],
    });

    const addAccount = () => {
        setAccounts([...accounts, { id: accounts.length + 1 }]);
        setData('accounts', [
            ...data.accounts,
            {
                user_id: '',
                first_name: '',
                last_name: '',
                date_of_birth: '',
                address: '',
                currency_id: '',
            },
        ]);
    };

    const removeAccount = (index: number) => {
        const newAccounts = accounts.filter((_, i) => i !== index);
        setAccounts(newAccounts);
        const newData = data.accounts.filter((_, i) => i !== index);
        setData('accounts', newData);
    };

    const handleUserSelect = (value: string, index: number) => {
        const selectedUser = users.find(user => user.id.toString() === value);
        if (selectedUser) {
            const newAccounts = [...data.accounts];
            newAccounts[index] = {
                ...newAccounts[index],
                user_id: value,
                first_name: selectedUser.first_name || '',
                last_name: selectedUser.last_name || '',
                date_of_birth: selectedUser.date_of_birth || '',
                address: selectedUser.address || '',
            };
            setData('accounts', newAccounts);
        }
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('savings-accounts.store'));
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Savings Accounts" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <Card>
                        <CardHeader>
                            <CardTitle>Create Savings Accounts</CardTitle>
                        </CardHeader>
                        <CardContent>
                            {users.length === 0 ? (
                                <div className="text-center py-4">
                                    <p className="text-gray-600">No users available. Please create some users first.</p>
                                </div>
                            ) : (
                                <form onSubmit={handleSubmit} className="space-y-6">
                                    {accounts.map((account, index) => (
                                        <div key={account.id} className="p-4 border rounded-lg space-y-4">
                                            <div className="flex justify-between items-center">
                                                <h3 className="text-lg font-medium">Account {index + 1}</h3>
                                                {index > 0 && (
                                                    <Button
                                                        type="button"
                                                        variant="destructive"
                                                        onClick={() => removeAccount(index)}
                                                    >
                                                        Remove
                                                    </Button>
                                                )}
                                            </div>

                                            <div className="grid grid-cols-2 gap-4">
                                                <div>
                                                    <Label htmlFor={`user_id_${index}`}>User</Label>
                                                    <Select
                                                        value={data.accounts[index].user_id}
                                                        onValueChange={(value) => handleUserSelect(value, index)}
                                                    >
                                                        <SelectTrigger>
                                                            <SelectValue placeholder="Select a user" />
                                                        </SelectTrigger>
                                                        <SelectContent>
                                                            {users.map((user) => (
                                                                <SelectItem key={user.id} value={user.id.toString()}>
                                                                    {user.name} ({user.email})
                                                                </SelectItem>
                                                            ))}
                                                        </SelectContent>
                                                    </Select>
                                                    {errors[`accounts.${index}.user_id`] && (
                                                        <p className="text-sm text-red-600">{errors[`accounts.${index}.user_id`]}</p>
                                                    )}
                                                </div>

                                                <div>
                                                    <Label htmlFor={`currency_id_${index}`}>Currency</Label>
                                                    <Select
                                                        value={data.accounts[index].currency_id}
                                                        onValueChange={(value) => {
                                                            const newAccounts = [...data.accounts];
                                                            newAccounts[index].currency_id = value;
                                                            setData('accounts', newAccounts);
                                                        }}
                                                    >
                                                        <SelectTrigger>
                                                            <SelectValue placeholder="Select a currency" />
                                                        </SelectTrigger>
                                                        <SelectContent>
                                                            {currencies.map((currency) => (
                                                                <SelectItem key={currency.id} value={currency.id.toString()}>
                                                                    {currency.code} - {currency.name} ({currency.symbol})
                                                                </SelectItem>
                                                            ))}
                                                        </SelectContent>
                                                    </Select>
                                                    {errors[`accounts.${index}.currency_id`] && (
                                                        <p className="text-sm text-red-600">{errors[`accounts.${index}.currency_id`]}</p>
                                                    )}
                                                </div>

                                                <div>
                                                    <Label htmlFor={`first_name_${index}`}>First Name</Label>
                                                    <Input
                                                        id={`first_name_${index}`}
                                                        value={data.accounts[index].first_name}
                                                        onChange={(e) => {
                                                            const newAccounts = [...data.accounts];
                                                            newAccounts[index].first_name = e.target.value;
                                                            setData('accounts', newAccounts);
                                                        }}
                                                    />
                                                    {errors[`accounts.${index}.first_name`] && (
                                                        <p className="text-sm text-red-600">{errors[`accounts.${index}.first_name`]}</p>
                                                    )}
                                                </div>

                                                <div>
                                                    <Label htmlFor={`last_name_${index}`}>Last Name</Label>
                                                    <Input
                                                        id={`last_name_${index}`}
                                                        value={data.accounts[index].last_name}
                                                        onChange={(e) => {
                                                            const newAccounts = [...data.accounts];
                                                            newAccounts[index].last_name = e.target.value;
                                                            setData('accounts', newAccounts);
                                                        }}
                                                    />
                                                    {errors[`accounts.${index}.last_name`] && (
                                                        <p className="text-sm text-red-600">{errors[`accounts.${index}.last_name`]}</p>
                                                    )}
                                                </div>

                                                <div>
                                                    <Label htmlFor={`date_of_birth_${index}`}>Date of Birth</Label>
                                                    <Input
                                                        id={`date_of_birth_${index}`}
                                                        type="date"
                                                        value={data.accounts[index].date_of_birth}
                                                        onChange={(e) => {
                                                            const newAccounts = [...data.accounts];
                                                            newAccounts[index].date_of_birth = e.target.value;
                                                            setData('accounts', newAccounts);
                                                        }}
                                                    />
                                                    {errors[`accounts.${index}.date_of_birth`] && (
                                                        <p className="text-sm text-red-600">{errors[`accounts.${index}.date_of_birth`]}</p>
                                                    )}
                                                </div>

                                                <div className="col-span-2">
                                                    <Label htmlFor={`address_${index}`}>Address</Label>
                                                    <Input
                                                        id={`address_${index}`}
                                                        value={data.accounts[index].address}
                                                        onChange={(e) => {
                                                            const newAccounts = [...data.accounts];
                                                            newAccounts[index].address = e.target.value;
                                                            setData('accounts', newAccounts);
                                                        }}
                                                    />
                                                    {errors[`accounts.${index}.address`] && (
                                                        <p className="text-sm text-red-600">{errors[`accounts.${index}.address`]}</p>
                                                    )}
                                                </div>
                                            </div>
                                        </div>
                                    ))}

                                    <div className="flex justify-between">
                                        <Button
                                            type="button"
                                            onClick={addAccount}
                                        >
                                            Add Another Account
                                        </Button>
                                        <Button
                                            type="submit"
                                            disabled={processing}
                                        >
                                            Create Accounts
                                        </Button>
                                    </div>
                                </form>
                            )}
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
}

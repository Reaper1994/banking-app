import { useState } from 'react';
import { Head, useForm } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type BreadcrumbItem } from '@/types';
import axios from 'axios';

interface TwoFactorResponse {
    qrCode: string;
    recoveryCodes: string[];
}

type TwoFactorFormData = {
    code: string;
    password: string;
    [key: string]: string;
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Settings',
        href: '/settings',
    },
    {
        title: 'Two Factor Authentication',
        href: '/settings/two-factor-authentication',
    },
];

export default function TwoFactorAuthentication() {
    const [qrCode, setQrCode] = useState<string | null>(null);
    const [recoveryCodes, setRecoveryCodes] = useState<string[]>([]);
    const [showRecoveryCodes, setShowRecoveryCodes] = useState(false);
    const [showPasswordConfirmation, setShowPasswordConfirmation] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [isEnabled, setIsEnabled] = useState(false);

    const { data, setData, post, processing, errors } = useForm<TwoFactorFormData>({
        code: '',
        password: '',
    });

    const enableTwoFactor = () => {
        console.log('Enabling 2FA...');
        setShowPasswordConfirmation(true);
    };

    const confirmPassword = () => {
        console.log('Confirming password...');
        axios.post(route('password.confirm'), {
            password: data.password,
        })
        .then(() => {
            console.log('Password confirmed, enabling 2FA...');
            setShowPasswordConfirmation(false);
            return axios.post(route('two-factor.enable'));
        })
        .then(() => {
            console.log('2FA enabled, fetching QR code...');
            return axios.get(route('two-factor.qr-code'));
        })
        .then((qrResponse) => {
            console.log('QR Code response:', qrResponse.data);
            setQrCode(qrResponse.data.svg);
            setIsEnabled(true);
        })
        .catch((error) => {
            console.error('Error:', error);
            setError(error.response?.data?.message || 'An error occurred. Please try again.');
        });
    };

    const confirmTwoFactor = () => {
        console.log('Confirming 2FA...');
        console.log('Sending code:', data.code);
        console.log('Route:', route('two-factor.enable'));

        axios.post(route('two-factor.enable'), {
            code: data.code,
        }, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            }
        })
        .then((response) => {
            console.log('Response:', response);
            console.log('2FA confirmed successfully');
            return axios.get(route('two-factor.recovery-codes'));
        })
        .then((response) => {
            console.log('Recovery codes response:', response.data);
            setRecoveryCodes(response.data);
            setShowRecoveryCodes(true);
        })
        .catch((error) => {
            console.error('Error details:', error.response?.data);
            console.error('Error status:', error.response?.status);
            console.error('Error headers:', error.response?.headers);
            setError(error.response?.data?.message || 'Invalid code. Please try again.');
        });
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Two Factor Authentication" />

            <div className="max-w-2xl mx-auto py-10 sm:px-6 lg:px-8">
                <Card>
                    <CardHeader>
                        <CardTitle>Two Factor Authentication</CardTitle>
                        <CardDescription>
                            Add additional security to your account using two factor authentication.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        {error && (
                            <div className="mb-4 p-4 bg-red-50 border border-red-200 rounded-md">
                                <p className="text-sm text-red-600">{error}</p>
                            </div>
                        )}
                        {showPasswordConfirmation ? (
                            <div className="space-y-4">
                                <p className="text-sm text-gray-600">
                                    Please confirm your password to continue.
                                </p>
                                <div className="mt-4">
                                    <Label htmlFor="password">Password</Label>
                                    <Input
                                        id="password"
                                        type="password"
                                        name="password"
                                        value={data.password}
                                        onChange={(e) => setData('password', e.target.value)}
                                        className="mt-1 block w-full"
                                        required
                                    />
                                    {errors.password && (
                                        <p className="mt-2 text-sm text-red-600">{errors.password}</p>
                                    )}
                                </div>
                                <Button onClick={confirmPassword} disabled={processing}>
                                    Confirm Password
                                </Button>
                            </div>
                        ) : !isEnabled ? (
                            <div className="space-y-4">
                                <p className="text-sm text-gray-600">
                                    When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone's Google Authenticator application.
                                </p>
                                <Button onClick={enableTwoFactor} disabled={processing}>
                                    Enable
                                </Button>
                            </div>
                        ) : (
                            <div className="space-y-4">
                                {showRecoveryCodes ? (
                                    <div className="space-y-4">
                                        <p className="text-sm text-gray-600">
                                            Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.
                                        </p>
                                        <div className="grid gap-1 max-w-xl mt-4 px-4 py-4 font-mono text-sm bg-gray-600 rounded-lg overflow-auto">
                                            {recoveryCodes.map((code) => (
                                                <div key={code}>{code}</div>
                                            ))}
                                        </div>
                                        <Button onClick={() => window.location.reload()} disabled={processing}>
                                            Done
                                        </Button>
                                    </div>
                                ) : (
                                    <div className="space-y-4">
                                        <p className="text-sm text-gray-600">
                                            Two factor authentication is now enabled. Scan the following QR code using your phone's authenticator application.
                                        </p>
                                        <div className="mt-4">
                                            {qrCode && <div dangerouslySetInnerHTML={{ __html: qrCode }} />}
                                        </div>
                                        <div className="mt-4">
                                            <Label htmlFor="code">Code</Label>
                                            <Input
                                                id="code"
                                                type="text"
                                                name="code"
                                                value={data.code}
                                                onChange={(e) => setData('code', e.target.value)}
                                                className="mt-1 block w-full"
                                                required
                                            />
                                            {errors.code && (
                                                <p className="mt-2 text-sm text-red-600">{errors.code}</p>
                                            )}
                                        </div>
                                        <Button onClick={confirmTwoFactor} disabled={processing}>
                                            Confirm
                                        </Button>
                                    </div>
                                )}
                            </div>
                        )}
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}

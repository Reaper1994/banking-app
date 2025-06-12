import { useState } from 'react';
import { Head, useForm } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type BreadcrumbItem } from '@/types';

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

    const { data, setData, post, processing, errors } = useForm({
        code: '',
    });

    const enableTwoFactor = () => {
        post(route('two-factor.enable'), {
            onSuccess: (response) => {
                setQrCode(response.qrCode);
                setRecoveryCodes(response.recoveryCodes);
                setShowRecoveryCodes(true);
            },
        });
    };

    const confirmTwoFactor = () => {
        post(route('two-factor.confirm'), {
            onSuccess: () => {
                setShowRecoveryCodes(false);
            },
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
                        {!qrCode ? (
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
                                        <div className="grid gap-1 max-w-xl mt-4 px-4 py-4 font-mono text-sm bg-gray-100 rounded-lg overflow-auto">
                                            {recoveryCodes.map((code) => (
                                                <div key={code}>{code}</div>
                                            ))}
                                        </div>
                                        <Button onClick={confirmTwoFactor} disabled={processing}>
                                            Confirm
                                        </Button>
                                    </div>
                                ) : (
                                    <div className="space-y-4">
                                        <p className="text-sm text-gray-600">
                                            Two factor authentication is now enabled. Scan the following QR code using your phone's authenticator application.
                                        </p>
                                        <div className="mt-4">
                                            <div dangerouslySetInnerHTML={{ __html: qrCode }} />
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

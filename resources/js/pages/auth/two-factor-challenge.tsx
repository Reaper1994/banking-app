import { useEffect } from 'react';
import { Head, useForm } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';

export default function TwoFactorChallenge() {
    const { data, setData, post, processing, errors, reset } = useForm({
        code: '',
        recovery_code: '',
    });

    useEffect(() => {
        return () => {
            reset('code', 'recovery_code');
        };
    }, []);

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('two-factor.login'));
    };

    return (
        <>
            <Head title="Two Factor Authentication" />

            <div className="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
                <div className="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                    <Card>
                        <CardHeader>
                            <CardTitle>Two Factor Authentication</CardTitle>
                            <CardDescription>
                                Please confirm access to your account by entering the authentication code provided by your authenticator application.
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <form onSubmit={submit}>
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

                                <div className="mt-4">
                                    <Label htmlFor="recovery_code">Recovery Code</Label>
                                    <Input
                                        id="recovery_code"
                                        type="text"
                                        name="recovery_code"
                                        value={data.recovery_code}
                                        onChange={(e) => setData('recovery_code', e.target.value)}
                                        className="mt-1 block w-full"
                                    />
                                    {errors.recovery_code && (
                                        <p className="mt-2 text-sm text-red-600">{errors.recovery_code}</p>
                                    )}
                                </div>

                                <div className="flex items-center justify-end mt-4">
                                    <Button type="submit" disabled={processing}>
                                        Verify
                                    </Button>
                                </div>
                            </form>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </>
    );
}

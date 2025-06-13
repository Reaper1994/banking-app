import { useState } from 'react';
import { Link } from '@inertiajs/react';
import { AppHeader } from '@/components/app-header';
import { AppSidebar } from '@/components/app-sidebar';
import { AppContent } from '@/components/app-content';
import { type BreadcrumbItem } from '@/types';

interface User {
    id: number;
    name: string;
    email: string;
    roles?: string[];
}

interface Props {
    user: User;
    header?: React.ReactNode;
    children: React.ReactNode;
    breadcrumbs?: BreadcrumbItem[];
}

export default function AppLayout({ user, header, children, breadcrumbs }: Props) {
    return (
        <div className="min-h-screen bg-gray-100">
            <AppHeader breadcrumbs={breadcrumbs} />
            <div className="flex">
                <AppSidebar />
                <AppContent>
                    {header && (
                        <header className="bg-white shadow">
                            <div className="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">{header}</div>
                        </header>
                    )}
                    <main>{children}</main>
                </AppContent>
            </div>
        </div>
    );
}

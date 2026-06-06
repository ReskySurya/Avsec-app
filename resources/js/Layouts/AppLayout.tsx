import { Head, usePage } from '@inertiajs/react';
import { ReactNode, useState } from 'react';
import FlashMessages from '../Components/FlashMessages';
import Navbar from '../Components/Navbar';
import Sidebar from '../Components/Sidebar';
import type { ApprovalCounts, SharedProps } from '../types';

type AppLayoutProps = {
    title?: string;
    approvalCounts?: ApprovalCounts;
    children: ReactNode;
};

type AppLayoutPageProps = SharedProps & {
    approvalCounts?: ApprovalCounts;
};

export default function AppLayout({ title, approvalCounts, children }: AppLayoutProps) {
    const { auth, flash, approvalCounts: sharedApprovalCounts } = usePage<AppLayoutPageProps>().props;
    const [sidebarOpen, setSidebarOpen] = useState(false);
    const csrfToken = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content ?? '';

    if (!auth.user) {
        return (
            <>
                {title && <Head title={title} />}
                <main className="px-4 py-4 sm:px-6 lg:px-8">
                    <div className="mx-auto max-w-7xl">{children}</div>
                </main>
            </>
        );
    }

    return (
        <>
            {title && <Head title={title} />}
            <div className="min-h-screen bg-gray-100">
                <Navbar
                    user={auth.user}
                    csrfToken={csrfToken}
                    sidebarOpen={sidebarOpen}
                    onToggleSidebar={() => setSidebarOpen((open) => !open)}
                />

                {sidebarOpen && (
                    <button
                        type="button"
                        aria-label="Close sidebar"
                        className="fixed inset-0 z-30 bg-black/50 lg:hidden"
                        onClick={() => setSidebarOpen(false)}
                    />
                )}

                <div className="flex h-full">
                    <Sidebar
                        user={auth.user}
                        csrfToken={csrfToken}
                        open={sidebarOpen}
                        approvalCounts={approvalCounts ?? sharedApprovalCounts}
                        onClose={() => setSidebarOpen(false)}
                    />
                    <main className="min-h-screen flex-1 px-4 pt-20 lg:ml-64 lg:px-8 lg:pt-8">
                        <div className="mx-auto space-y-6">
                            <FlashMessages flash={flash} />
                            {children}
                        </div>
                    </main>
                </div>
            </div>
        </>
    );
}

import { useState } from 'react';
import { StatsModal, StatsSection, type StatItem, type StatsModalState } from '../../Components/Dashboard/Stats';
import UserInfoCard from '../../Components/Dashboard/UserInfoCard';
import AppLayout from '../../Layouts/AppLayout';
import type { SharedProps } from '../../types';

type SuperadminDashboardProps = SharedProps & {
    dailyTestStats: Record<string, StatItem>;
    logbookStats: Record<string, StatItem>;
    checklistStats: Record<string, StatItem>;
};

export default function Dashboard({ auth, dailyTestStats, logbookStats, checklistStats }: SuperadminDashboardProps) {
    const [statsModal, setStatsModal] = useState<StatsModalState>(null);

    return (
        <AppLayout title="Superadmin Dashboard">
            <div className="mx-auto max-w-7xl space-y-6 py-6">
                <section className="rounded-lg bg-white p-6 shadow-md">
                    <h1 className="mb-2 text-3xl font-bold text-gray-900">Superadmin Dashboard</h1>
                    <p className="mb-6 text-gray-600">Selamat datang, {auth.user?.name}!</p>
                    <UserInfoCard user={auth.user} fields={['name', 'nip', 'email', 'role']} />
                </section>

                <StatsSection title="Persentase Penyelesaian Daily Test" stats={dailyTestStats} unit="form" accent="blue" onOpen={setStatsModal} />
                <StatsSection title="Persentase Penyelesaian Logbook" stats={logbookStats} unit="logbook" accent="green" onOpen={setStatsModal} />
                <StatsSection title="Persentase Penyelesaian Checklist" stats={checklistStats} unit="checklist" accent="purple" onOpen={setStatsModal} />
            </div>

            {statsModal && <StatsModal state={statsModal} onClose={() => setStatsModal(null)} />}
        </AppLayout>
    );
}

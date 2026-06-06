import { useState } from 'react';
import FormTypeModal, { type FormTypeModalState } from '../../Components/Dashboard/FormTypeModal';
import LinkedItemList from '../../Components/Dashboard/LinkedItemList';
import NotificationCard from '../../Components/Dashboard/NotificationCard';
import { StatsModal, StatsSection, type StatItem, type StatsModalState } from '../../Components/Dashboard/Stats';
import UserInfoCard from '../../Components/Dashboard/UserInfoCard';
import AppLayout from '../../Layouts/AppLayout';
import type { ApprovalCounts, SharedProps } from '../../types';

type ChiefReport = { id: number | string; creator?: string; date: string | null; detail_url: string; };
type DraftChiefReport = { id: number | string; date: string | null; detail_url: string; };

type SupervisorDashboardProps = SharedProps & {
    dailyTestStats: Record<string, StatItem>;
    logbookStats: Record<string, StatItem>;
    checklistStats: Record<string, StatItem>;
    pendingDailyTestCount: number;
    pendingLogbookCounts: Record<string, number>;
    totalPendingLogbooks: number;
    pendingChecklistCounts: Record<string, number>;
    totalPendingChecklists: number;
    pendingChiefReports: ChiefReport[];
    draftLogbookChief: DraftChiefReport[];
    approvalCounts: ApprovalCounts;
};

const formTypeLinks: Record<string, Record<string, string>> = {
    Logbook: {
        'Pos Jaga': '/logbook/logbook-form',
        'Laporan Chief': '/dashboard/supervisor',
        Rotasi: '/logbook-rotasi/list',
        'Manual Book': '/checklist-manual-book/list',
    },
    Checklist: {
        Kendaraan: '/checklist-kendaraan-patroli/list',
        Penyisiran: '/checklist-penyisiran-ruang-tunggu/list',
        'Pencatatan PI': '/form-pencatatan-pi/list',
    },
};

export default function Dashboard({
    auth,
    dailyTestStats,
    logbookStats,
    checklistStats,
    pendingDailyTestCount,
    pendingLogbookCounts,
    totalPendingLogbooks,
    pendingChecklistCounts,
    totalPendingChecklists,
    pendingChiefReports,
    draftLogbookChief,
    approvalCounts,
}: SupervisorDashboardProps) {
    const [statsModal, setStatsModal] = useState<StatsModalState>(null);
    const [formTypeModal, setFormTypeModal] = useState<FormTypeModalState>(null);

    return (
        <AppLayout title="Supervisor Dashboard" approvalCounts={approvalCounts}>
            <div className="mx-auto max-w-7xl space-y-6 py-6">
                <section className="rounded-lg bg-white p-6 shadow-md">
                    <h1 className="mb-4 text-3xl font-bold text-gray-900">Supervisor Dashboard</h1>
                    <p className="mb-6 text-gray-600">Selamat datang, {auth.user?.name}! Anda login sebagai Supervisor.</p>
                    <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <UserInfoCard user={auth.user} fields={['name', 'nip', 'email', 'role', 'lisensi']} />
                    </div>
                </section>

                <section className="rounded-lg bg-green-50 p-4 shadow-sm">
                    <h2 className="mb-4 text-lg font-semibold text-green-800">Notifikasi Supervisor</h2>
                    <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                        <NotificationCard href="/supervisor/dailytest-form" count={pendingDailyTestCount} title="Daily Test" tone="yellow" />
                        <NotificationCard count={totalPendingLogbooks} title="Logbook" tone="blue" onClick={() => setFormTypeModal({ title: 'Logbook', data: pendingLogbookCounts })} />
                        <NotificationCard count={totalPendingChecklists} title="Checklist" tone="teal" onClick={() => setFormTypeModal({ title: 'Checklist', data: pendingChecklistCounts })} />
                    </div>
                </section>

                {pendingChiefReports.length > 0 && (
                    <LinkedItemList title="Laporan Chief Menunggu Persetujuan" tone="purple" items={pendingChiefReports.map((item) => ({ ...item, label: `Laporan dari ${item.creator ?? 'N/A'}` }))} />
                )}

                {draftLogbookChief.length > 0 && (
                    <LinkedItemList title="Laporan Chief Dalam Proses" tone="orange" items={draftLogbookChief.map((item) => ({ ...item, label: 'Laporan Chief (Draft)' }))} />
                )}

                <StatsSection title="Persentase Penyelesaian Daily Test" stats={dailyTestStats} unit="form" accent="blue" onOpen={setStatsModal} />
                <StatsSection title="Persentase Penyelesaian Logbook" stats={logbookStats} unit="logbook" accent="green" onOpen={setStatsModal} />
                <StatsSection title="Persentase Penyelesaian Checklist" stats={checklistStats} unit="checklist" accent="purple" onOpen={setStatsModal} />
            </div>

            {statsModal && <StatsModal state={statsModal} onClose={() => setStatsModal(null)} />}
            {formTypeModal && <FormTypeModal state={formTypeModal} links={formTypeLinks} onClose={() => setFormTypeModal(null)} />}
        </AppLayout>
    );
}

import ActionLink from '../../Components/Dashboard/ActionLink';
import CardRow from '../../Components/Dashboard/CardRow';
import Pagination, { type PaginationLink } from '../../Components/Dashboard/Pagination';
import ReportSection from '../../Components/Dashboard/ReportSection';
import ResponsiveList from '../../Components/Dashboard/ResponsiveList';
import StatusSection, { type StatusGroup, type StatusItem } from '../../Components/Dashboard/StatusSection';
import UserInfoCard from '../../Components/Dashboard/UserInfoCard';
import AppLayout from '../../Layouts/AppLayout';
import type { SharedProps } from '../../types';

type RejectedReport = { id: number | string; date: string | null; equipment: string; location: string; reason: string | null; detail_url: string | null; };
type RejectedLogbook = { id: number | string; location: string; date: string | null; shift: string | null; reason: string | null; detail_url: string; };
type DashboardItem = { id: number | string; date: string | null; detail_url: string; };
type LogbookEntry = DashboardItem & { location: string; group: string | null; shift: string | null; sender: string; };
type ChecklistKendaraan = DashboardItem & { type: string | null; shift: string | null; sender: string; };
type ChecklistPenyisiran = DashboardItem & { time: string | null; type: string | null; group: string | null; sender: string; };
type DraftItem = DashboardItem & { location?: string; type?: string; };
type Paginated<T> = { data: T[]; links?: PaginationLink[]; };

type OfficerDashboardProps = SharedProps & {
    dailyTestStatuses: StatusGroup;
    checklistStatuses: StatusGroup;
    sweepingStatuses: StatusGroup;
    logbookSubmissionStatuses: StatusGroup;
    rejectedReports: RejectedReport[];
    rejectedLogbooks: Paginated<RejectedLogbook>;
    logbookEntries: LogbookEntry[];
    checklistKendaraan: ChecklistKendaraan[];
    checklistPenyisiran: ChecklistPenyisiran[];
    draftLogbooks: DraftItem[];
    draftLogbookRotasi: DraftItem[];
    draftManualBooks: DraftItem[];
};

export default function Dashboard({
    auth,
    dailyTestStatuses,
    checklistStatuses,
    sweepingStatuses,
    logbookSubmissionStatuses,
    rejectedReports,
    rejectedLogbooks,
    logbookEntries,
    checklistKendaraan,
    checklistPenyisiran,
    draftLogbooks,
    draftLogbookRotasi,
    draftManualBooks,
}: OfficerDashboardProps) {
    const allDrafts = [
        ...draftLogbooks.map((item) => ({ ...item, label: 'Logbook Pos Jaga', description: `${item.location ?? 'Tanpa Lokasi'} - ${item.date ?? '-'}` })),
        ...draftLogbookRotasi.map((item) => ({ ...item, label: 'Logbook Rotasi', description: `${item.type ?? '-'} - ${item.date ?? '-'}` })),
        ...draftManualBooks.map((item) => ({ ...item, label: 'Manual Book', description: `${item.type ?? '-'} - ${item.date ?? '-'}` })),
    ];

    return (
        <AppLayout title="Dashboard">
            <div className="mx-auto max-w-7xl space-y-6 py-6">
                <section className="rounded-lg bg-white p-6 shadow-md">
                    <h1 className="mb-4 text-3xl font-bold text-gray-900">Dashboard</h1>
                    <p className="mb-6 text-gray-600">Selamat datang, {auth.user?.name}!</p>
                    <UserInfoCard user={auth.user} fields={['name', 'nip']} />
                </section>

                <StatusSection title="Status Pengisian Form Harian (Daily Test)" emptyText="Belum ada form yang diisi hari ini." statuses={dailyTestStatuses} renderItem={renderDailyTestItem} />
                <StatusSection title="Status Pengisian Form Checklist" emptyText="Belum ada form checklist yang diisi hari ini." statuses={checklistStatuses} />
                <StatusSection title="Status Logbook Sweeping PI" emptyText="Belum ada logbook sweeping yang diisi hari ini." statuses={sweepingStatuses} />
                <StatusSection title="Status Pengisian Logbook Lainnya" emptyText="Belum ada logbook lain yang diisi hari ini." statuses={logbookSubmissionStatuses} />

                {rejectedReports.length > 0 && (
                    <ReportSection title="List Laporan Ditolak" tone="red">
                        <ResponsiveList items={rejectedReports} columns={['Tanggal', 'Jenis Laporan', 'Alasan Penolakan', 'Aksi']} renderRow={(report) => [report.date ?? '-', `${report.equipment.toUpperCase()} (${report.location})`, report.reason ?? 'Tidak ada alasan', report.detail_url ? <ActionLink href={report.detail_url} label="Lihat Detail" /> : '-']} renderCard={(report) => <CardRow title={`${report.equipment.toUpperCase()} - ${report.location}`} subtitle={report.date ?? '-'} note={report.reason ?? 'Tidak ada alasan'} href={report.detail_url} />} />
                    </ReportSection>
                )}

                {allDrafts.length > 0 && (
                    <ReportSection title="Draft dalam Proses" tone="yellow">
                        <div className="space-y-3">
                            {allDrafts.map((draft) => (
                                <div key={`${draft.label}-${draft.id}`} className="flex items-center justify-between rounded-lg border-l-4 border-yellow-400 bg-white p-3 shadow-sm">
                                    <div><p className="font-semibold text-gray-800">{draft.label}</p><p className="text-sm text-gray-500">{draft.description}</p></div>
                                    {draft.detail_url !== '#' && <ActionLink href={draft.detail_url} label="Lanjutkan" />}
                                </div>
                            ))}
                        </div>
                    </ReportSection>
                )}

                {rejectedLogbooks.data.length > 0 && (
                    <ReportSection title="Logbook yang Ditolak" tone="red">
                        <ResponsiveList items={rejectedLogbooks.data} columns={['Lokasi & Tanggal', 'Shift', 'Alasan Penolakan', 'Aksi']} renderRow={(logbook) => [`${logbook.location} - ${logbook.date ?? '-'}`, logbook.shift ?? '-', logbook.reason ?? 'Tidak ada alasan', <ActionLink href={logbook.detail_url} label="Lihat Detail" />]} renderCard={(logbook) => <CardRow title={logbook.location} subtitle={`${logbook.date ?? '-'} | Shift: ${logbook.shift ?? '-'}`} note={logbook.reason ?? 'Tidak ada alasan'} href={logbook.detail_url} />} />
                        <Pagination links={rejectedLogbooks.links ?? []} />
                    </ReportSection>
                )}

                {logbookEntries.length > 0 && (
                    <ReportSection title="List Logbook yang Diterima" tone="green">
                        <ResponsiveList items={logbookEntries} columns={['Tanggal', 'Area', 'Grup', 'Shift', 'Pengirim']} renderRow={(logbook) => [logbook.date ?? '-', logbook.location, logbook.group ?? '-', logbook.shift ?? '-', logbook.sender]} renderCard={(logbook) => <CardRow title={logbook.location} subtitle={`Grup: ${logbook.group ?? '-'} | Shift: ${logbook.shift ?? '-'}`} note={`Pengirim: ${logbook.sender}`} href={logbook.detail_url} />} />
                    </ReportSection>
                )}

                {checklistKendaraan.length > 0 && (
                    <ReportSection title="List Checklist Kendaraan yang Diterima" tone="green">
                        <ResponsiveList items={checklistKendaraan} columns={['Tanggal', 'Jenis Kendaraan', 'Shift', 'Pengirim']} renderRow={(checklist) => [checklist.date ?? '-', titleCase(checklist.type), titleCase(checklist.shift), checklist.sender]} renderCard={(checklist) => <CardRow title={`${titleCase(checklist.type)} Patroli`} subtitle={`Shift: ${titleCase(checklist.shift)} | ${checklist.date ?? '-'}`} note={`Pengirim: ${checklist.sender}`} href={checklist.detail_url} />} />
                    </ReportSection>
                )}

                {checklistPenyisiran.length > 0 && (
                    <ReportSection title="List Checklist Penyisiran yang Diterima" tone="green">
                        <ResponsiveList items={checklistPenyisiran} columns={['Tanggal', 'Jam', 'Tipe Pengecekan', 'Grup', 'Pengirim']} renderRow={(checklist) => [checklist.date ?? '-', checklist.time ?? '-', titleCase(checklist.type), titleCase(checklist.group), checklist.sender]} renderCard={(checklist) => <CardRow title={`${titleCase(checklist.type)} Ruang Tunggu`} subtitle={`Grup: ${titleCase(checklist.group)} | ${checklist.date ?? '-'}`} note={`Pengirim: ${checklist.sender}`} href={checklist.detail_url} />} />
                    </ReportSection>
                )}
            </div>
        </AppLayout>
    );
}

function renderDailyTestItem(item: StatusItem): string {
    return `${(item.equipment_name ?? '').toUpperCase()} - ${item.location_name ?? '-'}`;
}

function titleCase(value?: string | null): string {
    return value ? value.charAt(0).toUpperCase() + value.slice(1) : '-';
}

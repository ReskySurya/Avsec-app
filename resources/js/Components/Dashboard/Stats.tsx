export type BreakdownItem = {
    total: number;
    approved: number;
};

export type StatItem = {
    total: number;
    approved: number;
    percentage: number;
    breakdown: Record<string, BreakdownItem>;
    breakdownTitle?: string;
};

export type StatsModalState = {
    title: string;
    breakdownTitle: string;
    data: Record<string, BreakdownItem>;
} | null;

type Accent = 'blue' | 'green' | 'purple';

export function StatsSection({ title, stats, unit, accent, onOpen }: { title: string; stats: Record<string, StatItem>; unit: string; accent: Accent; onOpen: (state: StatsModalState) => void }) {
    const entries = Object.entries(stats);

    return (
        <section className="rounded-lg bg-white p-6 shadow-lg">
            <h2 className="mb-1 text-xl font-bold text-gray-800">{title}</h2>
            <p className="mb-4 border-b pb-2 text-sm text-gray-500">Statistik untuk hari ini.</p>
            {entries.length > 0 ? (
                <div className="grid grid-cols-1 gap-6 pt-4 md:grid-cols-2 lg:grid-cols-4">
                    {entries.map(([name, item]) => <StatsCard key={name} name={name} item={item} unit={unit} accent={accent} onOpen={onOpen} />)}
                </div>
            ) : (
                <p className="text-gray-500">Data statistik tidak tersedia.</p>
            )}
        </section>
    );
}

function StatsCard({ name, item, unit, accent, onOpen }: { name: string; item: StatItem; unit: string; accent: Accent; onOpen: (state: StatsModalState) => void }) {
    const accentText = { blue: 'text-blue-700', green: 'text-green-700', purple: 'text-purple-700' }[accent];
    const circumference = 2 * Math.PI * 42;
    const offset = circumference - (Math.min(item.percentage, 100) / 100) * circumference;

    return (
        <button type="button" className="rounded-xl border border-gray-200 bg-gray-50 p-5 text-left shadow-inner transition-colors duration-200 hover:bg-gray-100" onClick={() => onOpen({ title: name, data: item.breakdown ?? {}, breakdownTitle: item.breakdownTitle ?? 'Lokasi' })}>
            <h3 className="text-lg font-semibold text-gray-700">{name}</h3>
            <p className="mt-1 text-sm text-gray-500">{item.approved} dari {item.total} {unit} telah diajukan.</p>
            <div className="mt-4 flex items-center justify-center">
                <div className="relative h-28 w-28">
                    <svg className="h-28 w-28 -rotate-90" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="42" stroke="#e5e7eb" strokeWidth="10" fill="none" />
                        <circle cx="50" cy="50" r="42" stroke="currentColor" strokeWidth="10" fill="none" strokeDasharray={circumference} strokeDashoffset={offset} strokeLinecap="round" className={accentText} />
                    </svg>
                    <div className="absolute inset-0 flex flex-col items-center justify-center">
                        <span className="text-2xl font-bold text-gray-800">{item.percentage}</span>
                        <span className="text-[10px] uppercase tracking-wide text-gray-400">Percent</span>
                    </div>
                </div>
            </div>
            <p className={`mt-1 text-right text-sm font-semibold ${accentText}`}>{item.percentage}%</p>
        </button>
    );
}

export function StatsModal({ state, onClose }: { state: NonNullable<StatsModalState>; onClose: () => void }) {
    const entries = Object.entries(state.data);

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div className="w-full max-w-2xl rounded-2xl bg-white shadow-2xl">
                <header className="rounded-t-2xl bg-gradient-to-r from-blue-500 to-cyan-600 p-6 text-white">
                    <div className="flex items-center justify-between">
                        <h2 className="text-2xl font-bold">Detail untuk {state.title}</h2>
                        <button type="button" className="text-3xl leading-none text-blue-100 hover:text-white" onClick={onClose}>&times;</button>
                    </div>
                    <p className="text-blue-100">Rincian jumlah form per {state.breakdownTitle.toLowerCase()} untuk hari ini.</p>
                </header>
                <div className="max-h-[60vh] overflow-y-auto p-6">
                    <table className="min-w-full divide-y divide-gray-200">
                        <thead className="bg-gray-50">
                            <tr>
                                <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{state.breakdownTitle}</th>
                                <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Form Disetujui</th>
                                <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Total Form</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-200 bg-white">
                            {entries.length > 0 ? entries.map(([key, value]) => (
                                <tr key={key}>
                                    <td className="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">{key}</td>
                                    <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{value.approved}</td>
                                    <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{value.total}</td>
                                </tr>
                            )) : (
                                <tr><td colSpan={3} className="py-8 text-center text-gray-500">Tidak ada data rincian untuk ditampilkan.</td></tr>
                            )}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    );
}

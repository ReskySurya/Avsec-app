export type FormTypeModalState = {
    title: 'Logbook' | 'Checklist';
    data: Record<string, number>;
} | null;

export default function FormTypeModal({ state, links, onClose }: { state: NonNullable<FormTypeModalState>; links: Record<string, Record<string, string>>; onClose: () => void }) {
    const entries = Object.entries(state.data).filter(([, count]) => count > 0);

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
            <div className="w-full max-w-md rounded-xl bg-white shadow-2xl">
                <header className="flex items-center justify-between rounded-t-xl border-b bg-gray-50 p-4">
                    <h2 className="text-xl font-bold text-gray-800">Pilih Tipe Form {state.title}</h2>
                    <button type="button" className="text-3xl leading-none text-gray-400 hover:text-gray-600" onClick={onClose}>&times;</button>
                </header>
                <div className="p-6">
                    {entries.length > 0 ? (
                        <ul className="space-y-3">
                            {entries.map(([type, count]) => (
                                <li key={type}>
                                    <a href={links[state.title][type] ?? '#'} className="flex items-center justify-between rounded-lg bg-gray-100 p-4 transition-colors duration-200 hover:bg-gray-200">
                                        <span className="font-semibold text-gray-700">{type}</span>
                                        <span className="rounded-full bg-red-600 px-3 py-1 text-sm font-bold text-white">{count}</span>
                                    </a>
                                </li>
                            ))}
                        </ul>
                    ) : (
                        <p className="py-8 text-center text-gray-500">Tidak ada laporan yang menunggu persetujuan untuk kategori ini.</p>
                    )}
                </div>
            </div>
        </div>
    );
}

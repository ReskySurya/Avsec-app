export type StatusItem = {
    name?: string;
    equipment_name?: string;
    location_name?: string;
    form_link?: string;
};

export type StatusGroup = {
    submitted: StatusItem[];
    not_submitted?: StatusItem[];
};

export default function StatusSection({ title, emptyText, statuses, renderItem = defaultStatusItem }: { title: string; emptyText: string; statuses: StatusGroup; renderItem?: (item: StatusItem) => string }) {
    return (
        <section className="rounded-lg bg-gray-50 p-4 shadow-sm">
            <h2 className="mb-4 text-lg font-semibold text-gray-800">{title}</h2>
            <div className="rounded-lg bg-white p-3 shadow-sm">
                <h3 className="mb-2 font-semibold text-green-700">Sudah Diisi</h3>
                {statuses.submitted.length > 0 ? (
                    <ul className="space-y-2">
                        {statuses.submitted.map((item, index) => (
                            <li key={`${renderItem(item)}-${index}`} className="flex items-center p-2">
                                <span className="mr-2 text-green-500">✓</span>
                                <span className="text-sm text-gray-500 line-through">{renderItem(item)}</span>
                            </li>
                        ))}
                    </ul>
                ) : (
                    <p className="py-4 text-center text-sm text-gray-500">{emptyText}</p>
                )}
            </div>
        </section>
    );
}

function defaultStatusItem(item: StatusItem): string {
    return item.name ?? '-';
}

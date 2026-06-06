import ReportSection from './ReportSection';

type LinkedItem = {
    id: number | string;
    label: string;
    date: string | null;
    detail_url: string;
};

export default function LinkedItemList({ title, tone, items }: { title: string; tone: 'purple' | 'orange' | 'yellow'; items: LinkedItem[] }) {
    return (
        <ReportSection title={title} tone={tone}>
            <ul className="space-y-2">
                {items.map((item) => (
                    <li key={item.id}>
                        <a href={item.detail_url} className="block rounded-lg bg-white/60 p-3 transition-colors hover:bg-white">
                            <div className="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                <span className="font-semibold">{item.label}</span>
                                <span className="text-sm">{item.date ?? '-'}</span>
                            </div>
                        </a>
                    </li>
                ))}
            </ul>
        </ReportSection>
    );
}

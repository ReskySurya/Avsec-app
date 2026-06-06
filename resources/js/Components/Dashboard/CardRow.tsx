export default function CardRow({ title, subtitle, note, href }: { title: string; subtitle: string; note: string; href?: string | null }) {
    const content = (
        <div className="rounded-lg border-l-4 border-current bg-white p-4 shadow-sm">
            <div className="mb-3 flex items-start justify-between gap-3">
                <div>
                    <p className="text-sm font-medium uppercase text-gray-900">{title}</p>
                    <p className="text-xs text-gray-500">{subtitle}</p>
                </div>
            </div>
            <p className="rounded bg-gray-50 p-2 text-sm text-gray-600">{note}</p>
        </div>
    );

    return href ? <a href={href}>{content}</a> : content;
}

export type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

export default function Pagination({ links }: { links: PaginationLink[] }) {
    if (links.length === 0) {
        return null;
    }

    return (
        <div className="mt-6 flex flex-wrap gap-2">
            {links.map((link, index) => (
                link.url ? (
                    <a key={`${link.label}-${index}`} href={link.url} className={`rounded px-3 py-1 text-sm ${link.active ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'}`} dangerouslySetInnerHTML={{ __html: link.label }} />
                ) : (
                    <span key={`${link.label}-${index}`} className="rounded bg-gray-100 px-3 py-1 text-sm text-gray-400" dangerouslySetInnerHTML={{ __html: link.label }} />
                )
            ))}
        </div>
    );
}

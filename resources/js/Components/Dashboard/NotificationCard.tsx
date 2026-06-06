type Tone = 'yellow' | 'blue' | 'teal';

export default function NotificationCard({ count, title, description = 'Laporan menunggu persetujuan', tone, href, onClick }: { count: number; title: string; description?: string; tone: Tone; href?: string; onClick?: () => void }) {
    const styles = {
        yellow: 'bg-yellow-100 hover:bg-yellow-200 text-yellow-900',
        blue: 'bg-blue-100 hover:bg-blue-200 text-blue-900',
        teal: 'bg-teal-100 hover:bg-teal-200 text-teal-900',
    }[tone];

    const content = (
        <div className={`flex items-center space-x-4 rounded-lg p-6 shadow-sm transition-colors ${styles}`}>
            <div className="text-4xl font-bold">{count}</div>
            <div>
                <h3 className="text-lg font-semibold">{title}</h3>
                <p>{description}</p>
            </div>
        </div>
    );

    if (href) return <a href={href}>{content}</a>;
    return <button type="button" className="text-left" onClick={onClick}>{content}</button>;
}

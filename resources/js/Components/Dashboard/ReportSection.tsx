import type { ReactNode } from 'react';

type Tone = 'red' | 'yellow' | 'green' | 'purple' | 'orange' | 'blue';

export default function ReportSection({ title, tone, children }: { title: string; tone: Tone; children: ReactNode }) {
    const styles = {
        red: 'bg-red-50 text-red-800 border-red-100',
        yellow: 'bg-yellow-50 text-yellow-800 border-yellow-100',
        green: 'bg-green-50 text-green-800 border-green-100',
        purple: 'bg-purple-50 text-purple-800 border-purple-100',
        orange: 'bg-orange-50 text-orange-800 border-orange-100',
        blue: 'bg-blue-50 text-blue-800 border-blue-100',
    }[tone];

    return (
        <section className={`rounded-lg border p-4 shadow-sm ${styles}`}>
            <h2 className="mb-4 text-lg font-semibold">{title}</h2>
            {children}
        </section>
    );
}

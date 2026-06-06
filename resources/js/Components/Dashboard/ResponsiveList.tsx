import type { ReactNode } from 'react';

export default function ResponsiveList<T>({ items, columns, renderRow, renderCard }: { items: T[]; columns: string[]; renderRow: (item: T) => ReactNode[]; renderCard: (item: T) => ReactNode }) {
    return (
        <>
            <div className="hidden overflow-x-auto md:block">
                <table className="min-w-full rounded-lg bg-white shadow-sm">
                    <thead>
                        <tr className="bg-white/70">
                            {columns.map((column) => <th key={column} className="px-4 py-3 text-left font-semibold text-gray-700">{column}</th>)}
                        </tr>
                    </thead>
                    <tbody>
                        {items.map((item, rowIndex) => (
                            <tr key={rowIndex} className="border-b transition-colors hover:bg-gray-50">
                                {renderRow(item).map((cell, cellIndex) => <td key={cellIndex} className="px-4 py-3 text-sm text-gray-700">{cell}</td>)}
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
            <div className="space-y-4 md:hidden">
                {items.map((item, index) => <div key={index}>{renderCard(item)}</div>)}
            </div>
        </>
    );
}

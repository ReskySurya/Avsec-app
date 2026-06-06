import type { SharedProps } from '../types';

type FlashMessagesProps = {
    flash: SharedProps['flash'];
};

export default function FlashMessages({ flash }: FlashMessagesProps) {
    return (
        <div className="space-y-3">
            {flash.success && <FlashMessage tone="green" message={flash.success} />}
            {flash.warning && <FlashMessage tone="yellow" message={flash.warning} />}
            {flash.error && <FlashMessage tone="red" message={flash.error} />}
        </div>
    );
}

function FlashMessage({ tone, message }: { tone: 'green' | 'yellow' | 'red'; message: string }) {
    const styles = {
        green: 'border-green-200 bg-green-50 text-green-800',
        yellow: 'border-yellow-200 bg-yellow-50 text-yellow-800',
        red: 'border-red-200 bg-red-50 text-red-800',
    }[tone];

    return (
        <div className={`rounded-lg border px-4 py-3 text-sm shadow-sm ${styles}`} role="alert">
            {message}
        </div>
    );
}

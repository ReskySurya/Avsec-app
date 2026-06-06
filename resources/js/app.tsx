import './bootstrap';

import { createInertiaApp } from '@inertiajs/react';
import type { ComponentType } from 'react';
import { createRoot } from 'react-dom/client';

type PageModule = {
    default: ComponentType<Record<string, unknown>>;
};

createInertiaApp({
    title: (title) => (title ? `${title} - Avsec App` : 'Avsec App'),
    resolve: (name) => {
        const pages = import.meta.glob<PageModule>('./Pages/**/*.tsx', { eager: true });
        return pages[`./Pages/${name}.tsx`];
    },
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },
    progress: {
        color: '#2563eb',
    },
});

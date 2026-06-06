import type { ApprovalCounts, Role } from '../types';

export type SidebarMenuItem = {
    label: string;
    href?: string;
    roles: Role[];
    active?: string[];
    badgeKey?: keyof ApprovalCounts;
    children?: SidebarMenuItem[];
};

export const sidebarMenu: SidebarMenuItem[] = [
    {
        label: 'Dashboard',
        href: '/dashboard/:role',
        roles: ['superadmin', 'supervisor', 'officer'],
        active: ['/dashboard'],
    },
    {
        label: 'Master Data',
        roles: ['superadmin'],
        active: ['/master-data'],
        children: [
            { label: 'User Management', href: '/master-data/users-management', roles: ['superadmin'] },
            { label: 'Equipment location', href: '/master-data/equipment-locations', roles: ['superadmin'] },
            { label: 'Tenant Management', href: '/master-data/tenant-management', roles: ['superadmin'] },
            { label: 'Checklist Items', href: '/master-data/checklist-items', roles: ['superadmin'] },
        ],
    },
    {
        label: 'Daily Test',
        roles: ['officer'],
        active: ['/daily-test'],
        children: [
            { label: 'HHMD', href: '/daily-test/hhmd', roles: ['officer'] },
            { label: 'WTMD', href: '/daily-test/wtmd', roles: ['officer'] },
            {
                label: 'X-RAY',
                roles: ['officer'],
                active: ['/daily-test/xray'],
                children: [
                    { label: 'XRAY CABIN', href: '/daily-test/xraycabin', roles: ['officer'] },
                    { label: 'XRAY BAGASI', href: '/daily-test/xraybagasi', roles: ['officer'] },
                ],
            },
        ],
    },
    {
        label: 'Daily Test',
        href: '/supervisor/dailytest-form',
        roles: ['superadmin', 'supervisor'],
        active: ['/supervisor/dailytest-form'],
        badgeKey: 'reports',
    },
    {
        label: 'Logbook',
        roles: ['superadmin', 'supervisor', 'officer'],
        active: ['/logbook', '/logbook-rotasi', '/logbook-sweppingpi', '/sweepingpi'],
        badgeKey: 'totalLogbook',
        children: [
            { label: 'Logbook Pos Jaga', href: '/logbook/posjaga', roles: ['officer'] },
            { label: 'Logbook Sweeping PI', href: '/logbook-sweppingpi', roles: ['officer'] },
            { label: 'Logbook Pos Jaga', href: '/logbook/logbook-form', roles: ['superadmin', 'supervisor'], badgeKey: 'logbookPosJaga' },
            { label: 'Logbook Sweeping PI', href: '/logbook/sweepingpi', roles: ['superadmin', 'supervisor'] },
            { label: 'Logbook Rotasi', href: '/logbook-rotasi/list', roles: ['superadmin', 'supervisor'], badgeKey: 'logbookRotasi' },
            { label: 'Logbook Chief', href: '/logbook/chief', roles: ['superadmin', 'supervisor'] },
        ],
    },
    {
        label: 'Checklist',
        roles: ['superadmin', 'supervisor', 'officer'],
        active: ['/checklist', '/checklist-harian', '/form-pencatatan-pi'],
        badgeKey: 'totalChecklist',
        children: [
            { label: 'Checklist Kendaraan Patroli', href: '/checklist-harian-kendaraan', roles: ['officer'] },
            { label: 'Checklist Penyisiran Ruang Tunggu', href: '/checklist-harian-penyisiran-ruang-tunggu', roles: ['officer'] },
            { label: 'Checklist Senjata Api', href: '/checklist-senpi', roles: ['officer', 'superadmin', 'supervisor'] },
            { label: 'Form Pencatatan PI', href: '/checklist/form-pencatatan-pi', roles: ['officer'] },
            { label: 'Checklist Kendaraan Patroli', href: '/checklist-kendaraan-patroli/list', roles: ['superadmin', 'supervisor'], badgeKey: 'kendaraan' },
            { label: 'Checklist Penyisiran Ruang Tunggu', href: '/checklist-penyisiran-ruang-tunggu/list', roles: ['superadmin', 'supervisor'], badgeKey: 'penyisiran' },
            { label: 'Form Pencatatan PI', href: '/form-pencatatan-pi/list', roles: ['superadmin', 'supervisor'], badgeKey: 'pencatatanPI' },
            { label: 'Buku Pemeriksaan Manual', href: '/checklist-manual-book/list', roles: ['superadmin', 'supervisor'], badgeKey: 'manualBook' },
        ],
    },
    {
        label: 'Check Formulir',
        href: '/history',
        roles: ['officer', 'supervisor'],
        active: ['/history'],
    },
    {
        label: 'PM dan IK',
        href: '/pmik',
        roles: ['superadmin', 'supervisor', 'officer'],
        active: ['/pmik'],
    },
    {
        label: 'Export PDF',
        href: '/export',
        roles: ['superadmin'],
        active: ['/export'],
    },
];

export function filterMenuByRole(items: SidebarMenuItem[], role: Role): SidebarMenuItem[] {
    return items
        .filter((item) => item.roles.includes(role))
        .map((item) => ({
            ...item,
            children: item.children ? filterMenuByRole(item.children, role) : undefined,
        }))
        .filter((item) => item.href || (item.children && item.children.length > 0));
}

export function resolveHref(href: string, role: Role): string {
    return href.replace(':role', role);
}

export function isMenuItemActive(item: SidebarMenuItem, path: string, role: Role): boolean {
    if (item.href && normalizePath(path) === normalizePath(resolveHref(item.href, role))) {
        return true;
    }

    if (item.active?.some((activePath) => path.startsWith(activePath))) {
        return true;
    }

    return item.children?.some((child) => isMenuItemActive(child, path, role)) ?? false;
}

function normalizePath(path: string): string {
    return path.replace(/\/$/, '');
}

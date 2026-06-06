import { useState } from 'react';
import type { ApprovalCounts, AuthUser, Role } from '../types';
import { filterMenuByRole, isMenuItemActive, resolveHref, sidebarMenu, type SidebarMenuItem } from '../navigation/sidebarMenu';

type SidebarProps = {
    user: AuthUser;
    approvalCounts?: ApprovalCounts;
    open: boolean;
    csrfToken: string;
    onClose: () => void;
};

export default function Sidebar({ user, approvalCounts = {}, open, csrfToken, onClose }: SidebarProps) {
    const role = user.role ?? 'officer';
    const path = typeof window === 'undefined' ? '' : window.location.pathname;
    const items = filterMenuByRole(sidebarMenu, role);

    function closeOnMobile() {
        if (window.innerWidth < 1024) {
            onClose();
        }
    }

    return (
        <aside
            id="sidebar"
            className={`fixed left-0 top-16 z-40 h-screen w-72 overflow-y-auto bg-gray-800 p-4 text-white transition-transform duration-300 ease-in-out lg:top-0 lg:translate-x-0 lg:pt-4 ${open ? 'translate-x-0' : '-translate-x-full'}`}
        >
            <nav className="sm:mt-0 lg:mt-20">
                <ul className="space-y-2">
                    {items.map((item) => (
                        <MenuNode
                            key={itemKey(item, role)}
                            item={item}
                            role={role}
                            path={path}
                            approvalCounts={approvalCounts}
                            onNavigate={closeOnMobile}
                        />
                    ))}

                    <li className="hidden lg:block">
                        <form method="POST" action="/logout">
                            <input type="hidden" name="_token" value={csrfToken} />
                            <button type="submit" className="flex w-full items-center rounded p-2 text-left hover:bg-gray-700">
                                <span>Logout</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </nav>
        </aside>
    );
}

function MenuNode({
    item,
    role,
    path,
    approvalCounts,
    onNavigate,
    depth = 0,
}: {
    item: SidebarMenuItem;
    role: Role;
    path: string;
    approvalCounts: ApprovalCounts;
    onNavigate: () => void;
    depth?: number;
}) {
    const active = isMenuItemActive(item, path, role);
    const [open, setOpen] = useState(active);
    const badge = item.badgeKey ? approvalCounts[item.badgeKey] : undefined;

    if (item.children?.length) {
        return (
            <li>
                <button
                    type="button"
                    className={`flex w-full items-center justify-between rounded py-2 hover:bg-gray-700 ${depth > 0 ? 'px-4' : 'px-2'} ${active ? 'bg-gray-700/70' : ''}`}
                    onClick={() => setOpen((value) => !value)}
                >
                    <span>{item.label}</span>
                    <span className="flex items-center gap-2">
                        <Badge count={badge} />
                        <span className={`transition-transform ${open ? 'rotate-180' : ''}`}>⌄</span>
                    </span>
                </button>
                {open && (
                    <ul className={`mt-2 space-y-2 ${depth > 0 ? 'pl-4' : 'pl-8'}`}>
                        {item.children.map((child) => (
                            <MenuNode
                                key={itemKey(child, role)}
                                item={child}
                                role={role}
                                path={path}
                                approvalCounts={approvalCounts}
                                onNavigate={onNavigate}
                                depth={depth + 1}
                            />
                        ))}
                    </ul>
                )}
            </li>
        );
    }

    if (!item.href) {
        return null;
    }

    return (
        <li>
            <a
                href={resolveHref(item.href, role)}
                className={`flex items-center justify-between rounded py-2 hover:bg-gray-700 ${depth > 0 ? 'px-4' : 'px-2'} ${active ? 'bg-gray-700' : ''}`}
                onClick={onNavigate}
            >
                <span>{item.label}</span>
                <Badge count={badge} />
            </a>
        </li>
    );
}

function Badge({ count }: { count?: number }) {
    if (!count || count <= 0) {
        return null;
    }

    return <span className="rounded-full bg-amber-500 px-2 py-0.5 text-sm font-semibold leading-none text-white">{count}</span>;
}

function itemKey(item: SidebarMenuItem, role: Role): string {
    return item.href ? resolveHref(item.href, role) : item.label;
}

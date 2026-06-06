import type { AuthUser } from '../types';

type NavbarProps = {
    user: AuthUser;
    csrfToken: string;
    sidebarOpen: boolean;
    onToggleSidebar: () => void;
};

export default function Navbar({ user, csrfToken, sidebarOpen, onToggleSidebar }: NavbarProps) {
    return (
        <nav className="fixed left-0 right-0 top-0 z-50 bg-blue-800 text-white shadow-md">
            <div className="mx-auto px-4 sm:px-6 lg:px-8">
                <div className="flex h-16 justify-between">
                    <div className="flex">
                        <div className="mr-4 flex items-center lg:hidden">
                            <button
                                type="button"
                                className="inline-flex items-center justify-center rounded-md p-2 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                                aria-controls="sidebar"
                                aria-expanded={sidebarOpen}
                                onClick={onToggleSidebar}
                            >
                                <span className="sr-only">Open main menu</span>
                                {sidebarOpen ? <CloseIcon /> : <MenuIcon />}
                            </button>
                        </div>

                        <div className="flex flex-shrink-0 items-center">
                            <a href={`/dashboard/${user.role ?? 'officer'}`} className="text-xl font-bold text-white">
                                ASRS APP
                            </a>
                        </div>
                    </div>

                    <div className="flex items-center">
                        <div className="ml-3 flex items-center">
                            <span className="mr-2 hidden text-sm sm:block">{user.name}</span>
                            <form method="POST" action="/logout">
                                <input type="hidden" name="_token" value={csrfToken} />
                                <button type="submit" className="flex items-center rounded bg-red-700 px-3 py-1 text-sm text-white hover:bg-red-800">
                                    <span className="hidden sm:inline">Logout</span>
                                    <span className="sm:hidden">Out</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    );
}

function MenuIcon() {
    return (
        <svg className="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    );
}

function CloseIcon() {
    return (
        <svg className="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    );
}

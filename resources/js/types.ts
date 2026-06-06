export type Role = 'superadmin' | 'supervisor' | 'officer';

export type AuthUser = {
    id: number;
    name: string;
    email: string;
    nip?: string | null;
    lisensi?: string | null;
    role: Role | null;
};

export type SharedProps = {
    auth: {
        user: AuthUser | null;
    };
    flash: {
        success?: string | null;
        warning?: string | null;
        error?: string | null;
    };
};

export type ApprovalCounts = Partial<Record<
    | 'reports'
    | 'totalLogbook'
    | 'logbookPosJaga'
    | 'logbookRotasi'
    | 'totalChecklist'
    | 'kendaraan'
    | 'penyisiran'
    | 'pencatatanPI'
    | 'manualBook',
    number
>>;

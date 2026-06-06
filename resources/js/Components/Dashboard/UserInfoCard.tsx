import type { AuthUser } from '../../types';

export default function UserInfoCard({ user, fields = ['name', 'nip'] }: { user: AuthUser | null; fields?: Array<'name' | 'nip' | 'email' | 'role' | 'lisensi'> }) {
    const labels = { name: 'Nama', nip: 'NIP', email: 'Email', role: 'Role', lisensi: 'Lisensi' };

    return (
        <div className="rounded-lg bg-blue-50 p-4">
            <h2 className="mb-2 text-lg font-semibold text-blue-800">Informasi Pengguna</h2>
            <ul className="text-blue-600">
                {fields.map((field) => {
                    const value = user?.[field];
                    if (field === 'lisensi' && !value) return null;
                    return <li key={field}><strong>{labels[field]}:</strong> {value ?? '-'}</li>;
                })}
            </ul>
        </div>
    );
}

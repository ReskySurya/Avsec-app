import { Head, usePage } from '@inertiajs/react';
import { FormEvent, useMemo, useState } from 'react';

type ChangePasswordForm = {
    current_password: string;
    password: string;
    password_confirmation: string;
};

type ChangePasswordProps = {
    errors: Partial<Record<keyof ChangePasswordForm, string>>;
    flash: {
        success?: string | null;
        warning?: string | null;
        error?: string | null;
    };
};

type PasswordField = keyof ChangePasswordForm;

export default function ChangePassword() {
    const { errors, flash } = usePage<ChangePasswordProps>().props;
    const csrfToken = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content ?? '';
    const [visible, setVisible] = useState<Record<PasswordField, boolean>>({
        current_password: false,
        password: false,
        password_confirmation: false,
    });
    const [form, setForm] = useState<ChangePasswordForm>({
        current_password: '',
        password: '',
        password_confirmation: '',
    });

    const confirmationMismatch = useMemo(() => {
        return form.password_confirmation.length > 0 && form.password !== form.password_confirmation;
    }, [form.password, form.password_confirmation]);

    function toggle(field: PasswordField) {
        setVisible((current) => ({ ...current, [field]: !current[field] }));
    }

    function update(field: PasswordField, value: string) {
        setForm((current) => ({ ...current, [field]: value }));
    }

    function submit(event: FormEvent<HTMLFormElement>) {
        if (confirmationMismatch) {
            event.preventDefault();
        }
    }

    return (
        <>
            <Head title="Ganti Password" />
            <main className="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
                <div className="flex min-h-screen items-center justify-center p-4">
                    <div className="w-full max-w-md sm:max-w-lg">
                        <section className="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-xl">
                            <header className="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 pb-6 pt-8 sm:px-8">
                                <div className="text-center">
                                    <div className="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                                        <span className="text-2xl" aria-hidden="true">Lock</span>
                                    </div>
                                    <h1 className="mb-2 text-2xl font-bold text-gray-800 sm:text-3xl">Ganti Password Anda</h1>
                                    <p className="text-sm text-gray-600 sm:text-base">Untuk melanjutkan, silakan buat password baru yang aman.</p>
                                </div>
                            </header>

                            <div className="px-6 py-8 sm:px-8">
                                {flash.success && <Alert tone="green" message={flash.success} />}
                                {flash.warning && <Alert tone="yellow" message={flash.warning} />}
                                {flash.error && <Alert tone="red" message={flash.error} />}

                                <form action="/change-password" method="POST" className="space-y-6" onSubmit={submit}>
                                    <input type="hidden" name="_token" value={csrfToken} />

                                    <PasswordInput
                                        label="Password Saat Ini"
                                        name="current_password"
                                        value={form.current_password}
                                        visible={visible.current_password}
                                        error={errors.current_password}
                                        placeholder="Masukkan password saat ini"
                                        onChange={update}
                                        onToggle={toggle}
                                    />

                                    <PasswordInput
                                        label="Password Baru"
                                        name="password"
                                        value={form.password}
                                        visible={visible.password}
                                        error={errors.password}
                                        placeholder="Masukkan password baru"
                                        onChange={update}
                                        onToggle={toggle}
                                    />
                                    <p className="-mt-4 text-xs text-gray-500">Password harus minimal 8 karakter dengan kombinasi huruf dan angka</p>

                                    <PasswordInput
                                        label="Konfirmasi Password Baru"
                                        name="password_confirmation"
                                        value={form.password_confirmation}
                                        visible={visible.password_confirmation}
                                        error={confirmationMismatch ? 'Password baru dan konfirmasi password tidak cocok.' : undefined}
                                        placeholder="Konfirmasi password baru"
                                        onChange={update}
                                        onToggle={toggle}
                                    />

                                    <div className="pt-4">
                                        <button
                                            type="submit"
                                            className="flex w-full items-center justify-center rounded-xl border border-transparent bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-3 text-base font-semibold text-white shadow-lg transition-all duration-300 hover:scale-[1.02] hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 active:scale-[0.98]"
                                        >
                                            Update Password
                                        </button>
                                    </div>

                                    <div className="pt-4 text-center">
                                        <a href="/login" className="inline-flex items-center text-sm text-gray-600 transition-colors duration-300 hover:text-blue-600">
                                            Kembali
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </section>

                        <section className="mt-6 rounded-lg border border-blue-200 bg-blue-50 p-4">
                            <h2 className="mb-2 text-sm font-semibold text-blue-800">Tips Keamanan Password</h2>
                            <ul className="space-y-1 text-xs text-blue-700">
                                <li>Gunakan minimal 8 karakter</li>
                                <li>Kombinasikan huruf besar, kecil, angka, dan simbol</li>
                                <li>Jangan gunakan informasi pribadi</li>
                                <li>Gunakan password yang unik untuk setiap akun</li>
                            </ul>
                        </section>
                    </div>
                </div>
            </main>
        </>
    );
}

function Alert({ tone, message }: { tone: 'green' | 'yellow' | 'red'; message: string }) {
    const styles = {
        green: 'border-green-200 bg-green-50 text-green-700',
        yellow: 'border-yellow-200 bg-yellow-50 text-yellow-700',
        red: 'border-red-200 bg-red-50 text-red-700',
    }[tone];

    return (
        <div className={`mb-6 rounded-lg border p-4 text-sm ${styles}`} role="alert">
            {message}
        </div>
    );
}

function PasswordInput({
    label,
    name,
    value,
    visible,
    error,
    placeholder,
    onChange,
    onToggle,
}: {
    label: string;
    name: PasswordField;
    value: string;
    visible: boolean;
    error?: string;
    placeholder: string;
    onChange: (field: PasswordField, value: string) => void;
    onToggle: (field: PasswordField) => void;
}) {
    return (
        <div className="space-y-2">
            <label htmlFor={name} className="block text-sm font-semibold text-gray-700">
                {label} <span className="text-red-500">*</span>
            </label>
            <div className="relative">
                <input
                    type={visible ? 'text' : 'password'}
                    name={name}
                    id={name}
                    value={value}
                    required
                    onChange={(event) => onChange(name, event.target.value)}
                    className={`w-full rounded-xl border-2 px-4 py-3 pr-20 shadow-sm transition-all duration-300 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 ${error ? 'border-red-300 bg-red-50' : 'border-gray-200'}`}
                    placeholder={placeholder}
                />
                <button
                    type="button"
                    className="absolute inset-y-0 right-0 flex items-center pr-4 text-sm font-semibold text-gray-400 transition-colors hover:text-blue-600"
                    onClick={() => onToggle(name)}
                >
                    {visible ? 'Hide' : 'Show'}
                </button>
            </div>
            {error && <p className="mt-1 text-sm text-red-600">{error}</p>}
        </div>
    );
}

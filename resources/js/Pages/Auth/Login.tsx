import { Head, usePage } from '@inertiajs/react';
import { useState } from 'react';

type LoginForm = {
    identifier: string;
    password: string;
};

type LoginProps = {
    errors: Partial<Record<keyof LoginForm, string>>;
    identifier?: string;
};

export default function Login() {
    const [showPassword, setShowPassword] = useState(false);
    const { errors, identifier = '' } = usePage<LoginProps>().props;
    const [data, setData] = useState<LoginForm>({
        identifier,
        password: '',
    });
    const csrfToken = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content ?? '';

    return (
        <>
            <Head title="Login" />
            <main className="flex min-h-screen items-center justify-center bg-white p-4">
                <section className="relative w-full max-w-md">
                    <div className="rounded-2xl bg-white p-8 shadow-2xl">
                        <div className="text-center">
                            <div className="mx-auto mb-4 flex h-24 w-24 items-center justify-center">
                                <img src="/images/airport-security-logo.png" alt="Logo" className="mb-2 sm:mb-0" />
                            </div>
                            <span className="text-lg font-bold text-gray-800">Airport Security Reporting System</span>
                            <br />
                            <span className="text-lg font-bold text-gray-800">(ASRS)</span>
                        </div>

                        <div className="mt-3 text-center">
                            <p className="text-gray-500">Masuk ke akun Anda untuk melanjutkan</p>
                        </div>

                        {(errors.identifier || errors.password) && (
                            <div className="mt-6 rounded-lg border-l-4 border-red-500 bg-red-50 p-4">
                                <div className="flex items-center gap-2">
                                    <span className="text-red-500" aria-hidden="true">!</span>
                                    <div className="text-sm text-red-700">
                                        {errors.identifier ?? errors.password ?? 'Terjadi kesalahan. Silakan coba lagi.'}
                                    </div>
                                </div>
                            </div>
                        )}

                        <form method="POST" action="/login" className="space-y-6 pt-6">
                            <input type="hidden" name="_token" value={csrfToken} />
                            <div className="space-y-2">
                                <label htmlFor="identifier" className="block text-sm font-semibold text-gray-700">
                                    Email atau NIP
                                </label>
                                <div className="relative">
                                    <input
                                        type="text"
                                        name="identifier"
                                        id="identifier"
                                        value={data.identifier}
                                        onChange={(event) => setData((current) => ({ ...current, identifier: event.target.value }))}
                                        required
                                        autoFocus
                                        autoComplete="username"
                                        placeholder="Masukkan Email atau NIP Anda"
                                        className="input-focus w-full rounded-lg border border-gray-300 px-4 py-3 pl-12 outline-none transition-all duration-300 focus:border-transparent focus:ring-2 focus:ring-blue-500"
                                    />
                                    <span className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" aria-hidden="true">ID</span>
                                </div>
                            </div>

                            <div className="space-y-2">
                                <label htmlFor="password" className="block text-sm font-semibold text-gray-700">
                                    Password
                                </label>
                                <div className="relative">
                                    <input
                                        type={showPassword ? 'text' : 'password'}
                                        name="password"
                                        id="password"
                                        value={data.password}
                                        onChange={(event) => setData((current) => ({ ...current, password: event.target.value }))}
                                        required
                                        autoComplete="current-password"
                                        placeholder="Masukkan password Anda"
                                        className="input-focus w-full rounded-lg border border-gray-300 px-4 py-3 pl-12 pr-16 outline-none transition-all duration-300 focus:border-transparent focus:ring-2 focus:ring-blue-500"
                                    />
                                    <span className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" aria-hidden="true">Key</span>
                                    <button
                                        type="button"
                                        onClick={() => setShowPassword((value) => !value)}
                                        className="absolute right-4 top-1/2 -translate-y-1/2 text-sm font-semibold text-gray-400 transition-colors hover:text-gray-600"
                                    >
                                        {showPassword ? 'Hide' : 'Show'}
                                    </button>
                                </div>
                            </div>

                            <button
                                type="submit"
                                className="btn-hover w-full rounded-lg bg-gradient-to-r from-blue-500 to-purple-600 px-6 py-3 font-semibold text-white transition-all duration-300 hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-70"
                            >
                                Masuk
                            </button>
                        </form>
                    </div>
                </section>
            </main>
        </>
    );
}

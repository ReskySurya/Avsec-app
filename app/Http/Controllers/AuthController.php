<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect($this->redirectBasedOnRole());
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        // 1. Ubah validasi. Kita gunakan nama 'identifier' yang lebih umum.
        // Aturan 'email' dihapus agar bisa menerima NIP (yang bukan format email).
        $request->validate([
            'identifier' => 'required|string',
            'password' => 'required',
        ]);

        // 2. Ambil input dari request.
        $identifier = $request->input('identifier');
        $password = $request->input('password');

        // 3. Tentukan nama kolom (field) berdasarkan format identifier.
        // Jika input mengandung '@' atau merupakan email valid, kita anggap itu email.
        // Jika tidak, kita anggap itu NIP.
        $field = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'nip';

        // 4. Siapkan kredensial untuk percobaan login.
        $credentials = [
            $field => $identifier,
            'password' => $password,
        ];

        // 5. Coba lakukan autentikasi dengan kredensial yang sudah disiapkan.
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate(); // Regenerate session untuk keamanan

            return redirect($this->redirectBasedOnRole())
                ->with('success', 'Berhasil login!');
        }

        // 6. Jika gagal, kembalikan ke halaman sebelumnya dengan pesan error.
        // Pesan error dihubungkan dengan input 'identifier'.
        return back()->withErrors([
            'identifier' => 'Email/NIP atau password yang Anda masukkan salah.',
        ])->onlyInput('identifier'); // Mengembalikan input 'identifier' saja
    }

    public function logout()
    {
        Session::flush();
        Auth::logout();

        return redirect('login');
    }

    private function redirectBasedOnRole()
    {
        $user = Auth::user();
        $role = $user->role->name;

        switch ($role) {
            case 'superadmin':
                return '/dashboard/superadmin';
            case 'supervisor':
                return '/dashboard/supervisor';
            case 'officer':
                return '/dashboard/officer';
            default:
                return '/dashboard/officer';
        }
    }

    /**
     * Menampilkan form untuk mengganti password.
     */
    public function showChangePasswordForm()
    {
        return view('auth.change-password');
    }

    /**
     * Memproses permintaan untuk update password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', function ($attribute, $value, $fail) {
                if (!Hash::check($value, Auth::user()->password)) {
                    $fail('Password saat ini tidak cocok.');
                }
            }],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        $user = User::find(Auth::id());

        // Cek apakah password baru sama dengan password default "P4ssword"
        if ($request->password === 'P4ssword') {
            return back()->withErrors(['password' => 'Password baru tidak boleh sama dengan password default.']);
        }

        $user->password = Hash::make($request->password);
        $user->must_change_password = false; // Tandai bahwa user sudah mengganti password
        $user->save();

        // Arahkan ke dashboard yang sesuai setelah berhasil update
        return redirect($this->redirectBasedOnRole())->with('success', 'Password Anda berhasil diperbarui!');
    }
}

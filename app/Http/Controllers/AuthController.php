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
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            return redirect($this->redirectBasedOnRole())
                ->with('success', 'Berhasil login!');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
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

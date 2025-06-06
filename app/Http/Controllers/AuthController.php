<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

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
}

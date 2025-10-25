<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Tampilkan form login.
     */
    public function showLoginForm()
    {
        // Jika user sudah login, arahkan ke halaman dashboard
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        return view('auth.login');
    }

    /**
     * Tangani proses otentikasi.
     */
    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            // Validasi menggunakan 'username'
            'username' => ['required', 'string'],
            'password' => ['required'],
        ]);

        // Coba otentikasi menggunakan field 'username'
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Panggil fungsi redirect berdasarkan role
            return $this->redirectBasedOnRole(Auth::user());
        }

        return back()->withErrors([
            'username' => 'Username atau Password salah.',
        ])->onlyInput('username');
    }

    /**
     * Log out user.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
    
    /**
     * Logika Redirect berdasarkan Role.
     */
    protected function redirectBasedOnRole($user): RedirectResponse
    {
        switch ($user->role) {
            case 'Admin Gudang':
                return redirect()->intended('/admin/dashboard');
            case 'Staff Gudang':
                return redirect()->intended('/staff/dashboard');
            case 'Head of Depo':
                return redirect()->intended('/head/dashboard');
            default:
                return redirect()->intended('/login');
        }
    }
}
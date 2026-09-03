<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Models\AuditLog;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // 1. Validate the input
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Proteksi brute force — kunci sementara (per kombinasi email + IP)
        // kalau sudah gagal 5 kali beruntun, supaya password tidak bisa
        // ditebak ribuan kali per detik lewat script otomatis.
        $throttleKey = Str::lower($credentials['email']) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            AuditLog::record('Login Diblokir', sprintf(
                'Percobaan login untuk "%s" diblokir sementara karena terlalu banyak percobaan gagal.',
                $credentials['email']
            ));

            return back()->withErrors([
                'email' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.",
            ])->onlyInput('email');
        }

        // 3. Attempt to authenticate
        if (Auth::attempt($credentials)) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate(); // Prevent session fixation attacks
            AuditLog::record('Login', 'User berhasil login ke dalam sistem.');
            return redirect()->intended('dashboard');
        }

        // 4. If failed, count the attempt and return back with error
        RateLimiter::hit($throttleKey, 60);

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        AuditLog::record('Logout', 'User keluar dari sistem.');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}

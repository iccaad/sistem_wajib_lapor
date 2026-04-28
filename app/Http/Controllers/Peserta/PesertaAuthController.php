<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PesertaAuthController extends Controller
{
    /**
     * Display the participant NIK login form.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        // If already logged in as peserta, redirect to dashboard
        if (Auth::check() && Auth::user()->isPeserta()) {
            return redirect()->route('peserta.dashboard');
        }

        return view('peserta.login');
    }

    /**
     * Process participant NIK login.
     *
     * Validates NIK format, checks rate limit (5 attempts / 10 min / IP),
     * looks up a matching active peserta user, and logs them in without password.
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'nik' => ['required', 'string', 'digits:16'],
        ], [
            'nik.required' => 'NIK wajib diisi.',
            'nik.digits'   => 'NIK harus terdiri dari 16 digit.',
        ]);

        // ── Rate limiting: max 5 attempts per IP per 10 minutes ──
        $key = 'login.peserta.' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            $minutes = ceil($seconds / 60);

            return back()
                ->withInput()
                ->withErrors([
                    'nik' => "Terlalu banyak percobaan. Coba lagi dalam {$minutes} menit.",
                ]);
        }

        // Find active peserta user by NIK
        $user = User::where('nik', $request->nik)
            ->where('role', 'peserta')
            ->where('is_active', true)
            ->first();

        if (!$user) {
            // Increment attempt count (decay: 10 minutes = 600 seconds)
            RateLimiter::hit($key, 600);

            return back()
                ->withInput()
                ->withErrors(['nik' => 'NIK tidak ditemukan atau akun tidak aktif.']);
        }

        // Successful login — clear rate limit
        RateLimiter::clear($key);

        // Log in the participant (no password needed)
        Auth::login($user);

        // Regenerate session to prevent fixation
        $request->session()->regenerate();

        return redirect()->intended(route('peserta.dashboard'));
    }

    /**
     * Log the participant out.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}

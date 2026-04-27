<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
     * Validates NIK format, looks up a matching active peserta user,
     * and logs them in without password authentication.
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'nik' => ['required', 'string', 'digits:16'],
        ], [
            'nik.required' => 'NIK wajib diisi.',
            'nik.digits' => 'NIK harus terdiri dari 16 digit.',
        ]);

        // Find active peserta user by NIK
        $user = User::where('nik', $request->nik)
            ->where('role', 'peserta')
            ->where('is_active', true)
            ->first();

        if (!$user) {
            return back()
                ->withInput()
                ->withErrors(['nik' => 'NIK tidak ditemukan atau akun tidak aktif.']);
        }

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

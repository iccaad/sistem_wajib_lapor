<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AdminAccountController extends Controller
{
    /**
     * Display a listing of admin accounts.
     */
    public function index(Request $request): View
    {
        $perPage = $this->getPerPage($request, 'admin_accounts_per_page', 5);
        $admins = User::where('role', 'admin')
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return view('admin.accounts.index', compact('admins'));
    }

    /**
     * Show the form for creating a new admin account.
     */
    public function create(): View
    {
        return view('admin.accounts.create');
    }

    /**
     * Store a newly created admin account.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(6)],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'admin',
            'is_active' => true,
        ]);

        return redirect()->route('admin.accounts.index')
            ->with('success', 'Akun admin berhasil ditambahkan.');
    }

    /**
     * Show the form for editing an admin account.
     */
    public function edit(User $account): View
    {
        // Prevent editing the super admin account
        if ($account->is_root_super_admin || $account->role === 'super_admin') {
            abort(403, 'Akun utama tidak dapat diedit dari sini.');
        }

        return view('admin.accounts.edit', compact('account'));
    }

    /**
     * Update the specified admin account.
     */
    public function update(Request $request, User $account): RedirectResponse
    {
        if ($account->is_root_super_admin || $account->role === 'super_admin') {
            abort(403, 'Akun utama tidak dapat diedit dari sini.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$account->id,
            'password' => ['nullable', 'confirmed', Password::min(6)],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        $account->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if (! empty($validated['password'])) {
            $account->update(['password' => Hash::make($validated['password'])]);
        }

        return redirect()->route('admin.accounts.index')
            ->with('success', 'Akun admin berhasil diperbarui.');
    }

    /**
     * Remove the specified admin account.
     */
    public function destroy(User $account): RedirectResponse
    {
        if ($account->is_root_super_admin || $account->role === 'super_admin') {
            return redirect()->route('admin.accounts.index')
                ->with('error', 'Akun utama tidak dapat dihapus.');
        }

        if ($account->id === auth()->id()) {
            return redirect()->route('admin.accounts.index')
                ->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        $account->delete();

        return redirect()->route('admin.accounts.index')
            ->with('success', 'Akun admin berhasil dihapus.');
    }

    private function getPerPage(Request $request, string $key, int $default = 5): int
    {
        $allowed = [5, 10, 15, 20];
        $perPage = $request->query('per_page', session($key, $default));
        $perPage = in_array((int) $perPage, $allowed) ? (int) $perPage : $default;
        session([$key => $perPage]);

        return $perPage;
    }
}

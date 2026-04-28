<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Services\PeriodService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private readonly PeriodService $periodService) {}

    public function index(): View|RedirectResponse
    {
        $participant = Auth::user()->participantProfile;

        // Edge case: user is peserta but has no participant profile
        if (!$participant) {
            Auth::logout();
            return redirect('/login')->withErrors(['nik' => 'Profil peserta tidak ditemukan. Hubungi petugas.']);
        }

        $currentPeriod   = $this->periodService->getCurrentPeriod($participant);
        $attendedCount   = $currentPeriod?->attended_count ?? 0;
        $remainingCount  = $currentPeriod?->getRemainingCount() ?? 0;
        $remainingDays   = $participant->getRemainingDays();
        $hasAbsentToday  = $participant->hasAbsentToday();
        $isActive        = $participant->isActive();
        $quotaFull       = $currentPeriod?->isFulfilled() ?? false;

        $location = $participant->location()->where('is_active', true)->first();

        $activeWarnings  = $participant->warnings()
            ->where('status', 'active')
            ->latest('issued_at')
            ->get();

        $recentLogs = $participant->attendanceLogs()
            ->with('location')
            ->orderByDesc('attendance_date')
            ->orderByDesc('attendance_time')
            ->limit(10)
            ->get();

        return view('peserta.dashboard', compact(
            'participant',
            'currentPeriod',
            'attendedCount',
            'remainingCount',
            'remainingDays',
            'hasAbsentToday',
            'isActive',
            'quotaFull',
            'location',
            'activeWarnings',
            'recentLogs'
        ));
    }
}

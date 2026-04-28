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

        $activeLocations = $participant->locations()->where('is_active', true)->get();

        // Determine the next check-in order and its specific location
        $nextCheckInOrder = ($currentPeriod ? ($currentPeriod->attended_count ?? 0) : 0) + 1;
        $nextLocation = $participant->locations()
            ->wherePivot('check_in_order', $nextCheckInOrder)
            ->where('is_active', true)
            ->first();

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
            'activeLocations',
            'nextCheckInOrder',
            'nextLocation',
            'activeWarnings',
            'recentLogs'
        ));
    }
}

<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HistoryController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $participant = Auth::user()->participantProfile;

        if (!$participant) {
            return redirect()->route('peserta.dashboard');
        }

        // Load all periods with their attendance logs, newest first
        $periods = $participant->attendancePeriods()
            ->with(['attendanceLogs' => function ($q) {
                $q->with('location')
                  ->orderBy('attendance_date')
                  ->orderBy('attendance_time');
            }])
            ->orderByDesc('period_start')
            ->get();

        return view('peserta.history', compact('participant', 'periods'));
    }
}

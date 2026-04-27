<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendancePeriod;
use App\Models\Participant;
use App\Models\Warning;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Total peserta yang masih dalam masa pengawasan
        $totalActive = Participant::where('status', 'active')
            ->where('supervision_end', '>=', today())
            ->count();

        // Peserta yang periode aktifnya sudah fulfilled
        $totalCompliant = Participant::where('status', 'active')
            ->where('supervision_end', '>=', today())
            ->whereHas('attendancePeriods', function ($q) {
                $q->where('status', 'active')
                  ->where('period_start', '<=', today())
                  ->where('period_end', '>=', today())
                  ->whereColumn('attended_count', '>=', 'target_count');
            })
            ->count();

        // Peserta berisiko: periode aktif hampir habis (≤ 3 hari) dan belum terpenuhi
        $totalAtRisk = Participant::where('status', 'active')
            ->where('supervision_end', '>=', today())
            ->whereHas('attendancePeriods', function ($q) {
                $q->where('status', 'active')
                  ->where('period_start', '<=', today())
                  ->where('period_end', '>=', today())
                  ->where('period_end', '<=', today()->addDays(3))
                  ->whereColumn('attended_count', '<', 'target_count');
            })
            ->count();

        // Peserta mangkir: ada warning level_2 yang masih aktif
        $totalAbsent = Participant::where('status', 'active')
            ->whereHas('warnings', fn ($q) => $q->where('level', 'level_2')->where('status', 'active'))
            ->count();

        // Selesai masa pengawasan dalam 7 hari ke depan
        $endingSoon = Participant::where('status', 'active')
            ->whereBetween('supervision_end', [today(), today()->addDays(7)])
            ->count();

        // 8 peserta terbaru
        $recentParticipants = Participant::with(['user', 'assignedAdmin', 'attendancePeriods'])
            ->latest()
            ->limit(8)
            ->get();

        return view('admin.dashboard', compact(
            'totalActive',
            'totalCompliant',
            'totalAtRisk',
            'totalAbsent',
            'endingSoon',
            'recentParticipants'
        ));
    }
}

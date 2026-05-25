<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\FiltersParticipants;
use App\Http\Controllers\Controller;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    use FiltersParticipants;

    public function index(Request $request): View
    {
        $admins = User::where('role', 'admin')->orderBy('name')->get();

        // Build a base filter closure to apply consistently across all queries
        $applyFilters = function ($query) use ($request) {
            if ($dateFrom = $request->input('date_from')) {
                $query->whereDate('participants.created_at', '>=', $dateFrom);
            }

            if ($dateTo = $request->input('date_to')) {
                $query->whereDate('participants.created_at', '<=', $dateTo);
            }

            if ($adminId = $request->input('admin_id')) {
                $query->where('participants.assigned_admin_id', $adminId);
            }
        };

        // Total peserta yang masih dalam masa pengawasan
        $totalActive = Participant::where('status', 'active')
            ->where('supervision_end', '>=', today())
            ->tap($applyFilters)
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
            ->tap($applyFilters)
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
            ->tap($applyFilters)
            ->count();

        // Peserta mangkir: ada warning level_2 yang masih aktif
        $totalAbsent = Participant::where('status', 'active')
            ->whereHas('warnings', fn ($q) => $q->where('level', 'level_2')->where('status', 'active'))
            ->tap($applyFilters)
            ->count();

        // Selesai masa pengawasan dalam 7 hari ke depan
        $endingSoon = Participant::where('status', 'active')
            ->whereBetween('supervision_end', [today(), today()->addDays(7)])
            ->tap($applyFilters)
            ->count();

        $perPage = $this->getPerPage($request, 'dashboard_per_page', 5);
        $recentQuery = Participant::with(['user', 'assignedAdmin', 'attendancePeriods'])
            ->tap($applyFilters)
            ->latest();

        // Apply the 3 dynamic stat filters to the table
        $this->applyParticipantFilters($recentQuery, $request);

        $recentParticipants = $recentQuery->paginate($perPage)
            ->withQueryString();

        return view('admin.dashboard', compact(
            'totalActive',
            'totalCompliant',
            'totalAtRisk',
            'totalAbsent',
            'endingSoon',
            'recentParticipants',
            'admins'
        ));
    }

    private function getPerPage(Request $request, string $key, int $default = 10): int
    {
        $allowed = [5, 10, 15, 20];
        $perPage = $request->query('per_page', session($key, $default));
        $perPage = in_array((int) $perPage, $allowed) ? (int) $perPage : $default;
        session([$key => $perPage]);

        return $perPage;
    }
}

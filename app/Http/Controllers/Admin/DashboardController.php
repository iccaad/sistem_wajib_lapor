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

        // Total peserta aktif
        $totalActive = Participant::where('status', 'active')
            ->tap($applyFilters)
            ->count();

        // Peserta patuh: tidak ada warning aktif sama sekali
        $totalCompliant = Participant::where('status', 'active')
            ->whereDoesntHave('warnings', fn ($q) => $q->where('status', 'active'))
            ->tap($applyFilters)
            ->count();

        // Peserta berisiko: ada warning level_1 yang masih aktif
        $totalAtRisk = Participant::where('status', 'active')
            ->whereHas('warnings', fn ($q) => $q->where('status', 'active')->where('level', 'level_1'))
            ->tap($applyFilters)
            ->count();

        // Peserta mangkir: ada warning level_2 atau level_3 yang masih aktif
        $totalAbsent = Participant::where('status', 'active')
            ->whereHas('warnings', fn ($q) => $q->where('status', 'active')->whereIn('level', ['level_2', 'level_3']))
            ->tap($applyFilters)
            ->count();

        // Selesai masa pengawasan dalam 7 hari ke depan
        $endingSoon = Participant::where('status', 'active')
            ->whereBetween('supervision_end', [today(), today()->addDays(7)])
            ->tap($applyFilters)
            ->count();

        // Telah selesai masa pengawasan
        $completed = Participant::where('status', 'active')
            ->where('supervision_end', '<', today())
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
            'completed',
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

    public function participantsModal(Request $request)
    {
        $filterType = $request->query('filter_type');
        
        switch ($filterType) {
            case 'aktif':
                $request->merge(['status_akun' => 'aktif']);
                break;
            case 'patuh':
                $request->merge(['status_akun' => 'aktif', 'tingkat_kepatuhan' => 'patuh']);
                break;
            case 'berisiko':
                $request->merge(['status_akun' => 'aktif', 'tingkat_kepatuhan' => 'berisiko']);
                break;
            case 'mangkir':
                $request->merge(['status_akun' => 'aktif', 'tingkat_kepatuhan' => 'mangkir']);
                break;
            case 'segera_selesai':
                $request->merge(['status_akun' => 'aktif', 'progress_pengawasan' => 'segera_selesai']);
                break;
            case 'selesai':
                $request->merge(['status_akun' => 'aktif', 'progress_pengawasan' => 'selesai']);
                break;
        }

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

        $query = Participant::with(['user', 'assignedAdmin', 'attendancePeriods'])
            ->tap($applyFilters)
            ->latest();

        $this->applyParticipantFilters($query, $request);

        // Fetch without pagination to keep the modal simple and straightforward
        $participants = $query->take(100)->get();

        return view('admin.partials.dashboard-modal-table', compact('participants'))->render();
    }
}

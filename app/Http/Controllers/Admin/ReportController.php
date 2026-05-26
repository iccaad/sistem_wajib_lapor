<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ParticipantsReportExport;
use App\Http\Controllers\Admin\Concerns\FiltersParticipants;
use App\Http\Controllers\Controller;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    use FiltersParticipants;

    public function index(Request $request): View
    {
        $admins = User::where('role', 'admin')->orderBy('name')->get();

        $query = Participant::with(['attendancePeriods', 'warnings', 'violationType', 'assignedAdmin']);

        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        if ($adminId = $request->input('admin_id')) {
            $query->where('assigned_admin_id', $adminId);
        }

        // Apply the 3 dynamic stat filters
        $this->applyParticipantFilters($query, $request);

        $paginated = $query->latest()->paginate(10)->withQueryString();

        // Enrich each row with computed stats (safe with Paginator)
        $paginated->getCollection()->transform(function ($p) {
            $periods = $p->attendancePeriods;
            $totalAttended = $periods->sum('attended_count');
            $totalTarget = $periods->sum('target_count');

            $p->total_periods = $periods->count();
            $p->total_attended = $totalAttended;
            $p->total_target = $totalTarget;
            $p->compliance_percent = $totalTarget > 0
                ? round($totalAttended / $totalTarget * 100, 1)
                : 0;

            return $p;
        });

        $participants = $paginated;

        return view('admin.reports.index', compact('participants', 'admins'));
    }

    public function export(Request $request)
    {
        $query = Participant::with(['attendancePeriods', 'warnings', 'violationType', 'assignedAdmin']);

        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        if ($adminId = $request->input('admin_id')) {
            $query->where('assigned_admin_id', $adminId);
        }

        // Apply the 3 dynamic stat filters
        $this->applyParticipantFilters($query, $request);

        $participants = $query->latest()->get();

        return Excel::download(new ParticipantsReportExport($participants), 'laporan_peserta_'.date('Y-m-d_H-i-s').'.xlsx');
    }

    public function show(Participant $participant): View
    {
        $participant->load([
            'attendancePeriods.attendanceLogs.location',
            'warnings',
            'assignedAdmin',
            'user',
        ]);

        return view('admin.reports.show', compact('participant'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Participant;
use App\Models\User;
use App\Models\ViolationType;
use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Exports\ParticipantsReportExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $admins = User::where('role', 'admin')->orderBy('name')->get();

        $query = Participant::with(['attendancePeriods', 'warnings', 'violationType', 'assignedAdmin']);

        if ($filter = $request->input('violation_type_id')) {
            $query->where('violation_type_id', $filter);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        if ($adminId = $request->input('admin_id')) {
            $query->where('assigned_admin_id', $adminId);
        }

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

        $violationTypes = ViolationType::all();

        return view('admin.reports.index', compact('participants', 'violationTypes', 'admins'));
    }

    public function export(Request $request)
    {
        $query = Participant::with(['attendancePeriods', 'warnings', 'violationType', 'assignedAdmin']);

        if ($filter = $request->input('violation_type_id')) {
            $query->where('violation_type_id', $filter);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        if ($adminId = $request->input('admin_id')) {
            $query->where('assigned_admin_id', $adminId);
        }

        $participants = $query->latest()->get();

        return Excel::download(new ParticipantsReportExport($participants), 'laporan_peserta_' . date('Y-m-d_H-i-s') . '.xlsx');
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

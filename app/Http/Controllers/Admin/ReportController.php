<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $query = Participant::with(['attendancePeriods', 'warnings', 'violationType']);

        if ($filter = $request->input('violation_type_id')) {
            $query->where('violation_type_id', $filter);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $paginated = $query->latest()->paginate(10)->withQueryString();

        // Enrich each row with computed stats (safe with Paginator)
        $paginated->getCollection()->transform(function ($p) {
            $periods           = $p->attendancePeriods;
            $totalAttended     = $periods->sum('attended_count');
            $totalTarget       = $periods->sum('target_count');

            $p->total_periods      = $periods->count();
            $p->total_attended     = $totalAttended;
            $p->total_target       = $totalTarget;
            $p->compliance_percent = $totalTarget > 0
                ? round($totalAttended / $totalTarget * 100, 1)
                : 0;

            return $p;
        });

        $participants = $paginated;

        $violationTypes = \App\Models\ViolationType::all();

        return view('admin.reports.index', compact('participants', 'violationTypes'));
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

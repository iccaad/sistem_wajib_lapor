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
        $query = Participant::with(['attendancePeriods', 'warnings']);

        if ($filter = $request->input('violation_type')) {
            $query->where('violation_type', 'ilike', "%{$filter}%");
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $participants = $query->latest()->get()->map(function ($p) {
            $periods           = $p->attendancePeriods;
            $totalPeriods      = $periods->count();
            $totalAttended     = $periods->sum('attended_count');
            $totalTarget       = $periods->sum('target_count');
            $compliancePercent = $totalTarget > 0
                ? round($totalAttended / $totalTarget * 100, 1)
                : 0;

            return array_merge($p->toArray(), [
                '_model'             => $p,
                'total_periods'      => $totalPeriods,
                'total_attended'     => $totalAttended,
                'total_target'       => $totalTarget,
                'compliance_percent' => $compliancePercent,
            ]);
        });

        return view('admin.reports.index', compact('participants'));
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

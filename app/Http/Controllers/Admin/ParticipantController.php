<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreParticipantRequest;
use App\Http\Requests\Admin\UpdateParticipantRequest;
use App\Models\ActivityLog;
use App\Models\Location;
use App\Models\Participant;
use App\Models\User;
use App\Services\PeriodService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ParticipantController extends Controller
{
    /**
     * Display a listing of participants.
     * Supports search by name/NIK and pagination.
     */
    public function index(Request $request): View
    {
        $query = Participant::with('user', 'assignedAdmin')
            ->latest();

        // Search filter
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'ilike', "%{$search}%")
                  ->orWhere('nik', 'ilike', "%{$search}%");
            });
        }

        $participants = $query->paginate(10)->withQueryString();

        return view('admin.participants.index', compact('participants'));
    }

    /**
     * Show the form for creating a new participant.
     */
    public function create(): View
    {
        $locations = Location::active()->get();
        $violationTypes = \App\Models\ViolationType::all();

        return view('admin.participants.create', compact('locations', 'violationTypes'));
    }

    /**
     * Store a newly created participant.
     * Creates both User (peserta) and Participant records atomically.
     */
    public function store(StoreParticipantRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated) {
            // Create user account (peserta, no email, no password)
            $user = User::create([
                'name'      => $validated['full_name'],
                'nik'       => $validated['nik'],
                'role'      => 'peserta',
                'is_active' => $validated['status'] === 'active',
            ]);

            // Create participant profile
            $participant = Participant::create([
                'user_id'          => $user->id,
                'assigned_admin_id'=> auth()->id(),
                'full_name'        => $validated['full_name'],
                'nik'              => $validated['nik'],
                'address'          => $validated['address'],
                'phone'            => $validated['phone'],
                'violation_type_id'=> $validated['violation_type_id'],
                'case_notes'       => $validated['case_notes'],
                'supervision_start'=> $validated['supervision_start'],
                'supervision_end'  => $validated['supervision_end'],
                'quota_type'       => $validated['quota_type'],
                'quota_amount'     => $validated['quota_amount'],
                'status'           => $validated['status'],
                'location_id'      => $validated['location_id'],
            ]);

            // Auto-generate all attendance periods
            (new PeriodService())->generateAllPeriods($participant);



            // Log the action
            ActivityLog::create([
                'user_id'     => auth()->id(),
                'action'      => 'created_participant',
                'target_type' => 'participant',
                'target_id'   => $participant->id,
                'description' => 'Mendaftarkan peserta baru: ' . $validated['full_name'] . ' (NIK: ' . $validated['nik'] . ')',
                'ip_address'  => request()->ip(),
            ]);
        });

        return redirect()
            ->route('admin.participants.index')
            ->with('success', 'Peserta berhasil ditambahkan.');
    }

    /**
     * Display the specified participant detail page.
     */
    public function show(Participant $participant): View
    {
        $participant->load([
            'user',
            'assignedAdmin',
            'warnings',
            'attendanceLogs',
            'attendancePeriods',
            'location',
            'violationType',
        ]);

        return view('admin.participants.show', compact('participant'));
    }

    /**
     * Show the form for editing the specified participant.
     */
    public function edit(Participant $participant): View
    {
        $participant->load('user');
        $locations = Location::active()->get();
        $violationTypes = \App\Models\ViolationType::all();

        return view('admin.participants.edit', compact('participant', 'locations', 'violationTypes'));
    }

    /**
     * Update the specified participant.
     * Updates both User and Participant records atomically.
     */
    public function update(UpdateParticipantRequest $request, Participant $participant): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $participant) {
            // Update user account
            $participant->user->update([
                'name' => $validated['full_name'],
                'nik' => $validated['nik'],
                'is_active' => $validated['status'] === 'active',
            ]);

            // Update participant profile
            $participant->update([
                'full_name' => $validated['full_name'],
                'nik' => $validated['nik'],
                'address' => $validated['address'],
                'phone' => $validated['phone'],
                'violation_type_id' => $validated['violation_type_id'],
                'case_notes' => $validated['case_notes'],
                'supervision_start' => $validated['supervision_start'],
                'supervision_end' => $validated['supervision_end'],
                'quota_type' => $validated['quota_type'],
                'quota_amount' => $validated['quota_amount'],
                'status' => $validated['status'],
                'location_id' => $validated['location_id'],
            ]);

            // Sync attendance periods to reflect new quota or dates
            (new \App\Services\PeriodService())->syncPeriodsForUpdate($participant);

            // Log the action
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'updated_participant',
                'target_type' => 'participant',
                'target_id' => $participant->id,
                'description' => 'Memperbarui data peserta: ' . $validated['full_name'],
                'ip_address' => request()->ip(),
            ]);
        });

        return redirect()
            ->route('admin.participants.show', $participant)
            ->with('success', 'Data peserta berhasil diperbarui.');
    }

    /**
     * Soft-delete: deactivate the participant instead of deleting.
     * Sets both user.is_active and participant.status to inactive.
     */
    public function destroy(Participant $participant): RedirectResponse
    {
        DB::transaction(function () use ($participant) {
            $participant->user->update(['is_active' => false]);
            $participant->update(['status' => 'inactive']);

            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'deactivated_participant',
                'target_type' => 'participant',
                'target_id' => $participant->id,
                'description' => 'Menonaktifkan peserta: ' . $participant->full_name,
                'ip_address' => request()->ip(),
            ]);
        });

        return redirect()
            ->route('admin.participants.index')
            ->with('success', 'Peserta berhasil dinonaktifkan.');
    }
}

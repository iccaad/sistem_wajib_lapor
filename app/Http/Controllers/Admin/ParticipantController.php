<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreParticipantRequest;
use App\Http\Requests\Admin\UpdateParticipantRequest;
use App\Models\ActivityLog;
use App\Models\Location;
use App\Models\Participant;
use App\Models\User;
use App\Models\ViolationType;
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

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'ilike', "%{$search}%")
                    ->orWhere('nik', 'ilike', "%{$search}%");
            });
        }

        $perPage = $this->getPerPage($request, 'participants_per_page', 10);
        $participants = $query->paginate($perPage)->withQueryString();

        return view('admin.participants.index', compact('participants'));
    }

    private function getPerPage(Request $request, string $key, int $default = 10): int
    {
        $allowed = [5, 10, 15, 20];
        $perPage = $request->query('per_page', session($key, $default));
        $perPage = in_array((int) $perPage, $allowed) ? (int) $perPage : $default;
        session([$key => $perPage]);

        return $perPage;
    }

    /**
     * Show the form for creating a new participant.
     */
    public function create(): View
    {
        $locations = Location::active()->get();
        $violationTypes = ViolationType::all();

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
                'name' => $validated['full_name'],
                'nik' => $validated['nik'],
                'role' => 'peserta',
                'is_active' => $validated['status'] === 'active',
            ]);

            // Create participant profile
            $participant = Participant::create([
                'user_id' => $user->id,
                'assigned_admin_id' => auth()->id(),
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

            // Auto-generate all attendance periods
            (new PeriodService)->generateAllPeriods($participant);

            // Log the action
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'created_participant',
                'target_type' => 'participant',
                'target_id' => $participant->id,
                'description' => 'Mendaftarkan peserta baru: '.$validated['full_name'].' (NIK: '.$validated['nik'].')',
                'ip_address' => request()->ip(),
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

        $deletionCode = $participant->generateDeletionCode();
        session(['participant_deletion_code_'.$participant->id => $deletionCode]);

        return view('admin.participants.show', compact('participant', 'deletionCode'));
    }

    /**
     * Show the form for editing the specified participant.
     */
    public function edit(Participant $participant): View
    {
        $participant->load('user');
        $locations = Location::active()->get();
        $violationTypes = ViolationType::all();

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
            (new PeriodService)->syncPeriodsForUpdate($participant);

            // Log the action
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'updated_participant',
                'target_type' => 'participant',
                'target_id' => $participant->id,
                'description' => 'Memperbarui data peserta: '.$validated['full_name'],
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
                'description' => 'Menonaktifkan peserta: '.$participant->full_name,
                'ip_address' => request()->ip(),
            ]);
        });

        return redirect()
            ->route('admin.participants.index')
            ->with('success', 'Peserta berhasil dinonaktifkan.');
    }

    /**
     * Permanently delete the participant and all related data.
     */
    public function forceDelete(Request $request, Participant $participant): RedirectResponse
    {
        $request->validate([
            'deletion_code' => ['required', 'string'],
        ]);

        $storedCode = session('participant_deletion_code_'.$participant->id);

        if ($request->input('deletion_code') !== $storedCode) {
            return back()->withErrors(['deletion_code' => 'Kode konfirmasi tidak cocok.']);
        }

        session()->forget('participant_deletion_code_'.$participant->id);

        DB::transaction(function () use ($participant) {
            $participantName = $participant->full_name;
            $userId = $participant->user_id;

            // Delete related records
            $participant->attendanceLogs()->delete();
            $participant->attendanceAttempts()->delete();
            $participant->attendancePeriods()->delete();
            $participant->warnings()->delete();

            // Delete participant
            $participant->delete();

            // Delete associated user
            User::where('id', $userId)->delete();

            // Log the action
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'force_deleted_participant',
                'target_type' => 'participant',
                'target_id' => $participant->id,
                'description' => 'Menghapus permanen peserta: '.$participantName,
                'ip_address' => request()->ip(),
            ]);
        });

        return redirect()
            ->route('admin.participants.index')
            ->with('success', 'Peserta berhasil dihapus permanen.');
    }
}

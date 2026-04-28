<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Services\AttendanceService;
use App\Services\PeriodService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AbsenceController extends Controller
{
    public function __construct(
        private readonly AttendanceService $attendanceService,
        private readonly PeriodService $periodService,
    ) {}

    /**
     * Show the absence submission page.
     * Pre-validates conditions and redirects early if attendance is not possible.
     */
    public function show(): View|RedirectResponse
    {
        $participant = Auth::user()->participantProfile;

        if (!$participant) {
            return redirect()->route('peserta.dashboard');
        }

        if (!$participant->isActive()) {
            return redirect()->route('peserta.dashboard')
                ->with('error', 'Masa pengawasan Anda sudah berakhir.');
        }

        if ($participant->hasAbsentToday()) {
            return redirect()->route('peserta.dashboard')
                ->with('error', 'Anda sudah melakukan absensi hari ini. ✓');
        }

        $currentPeriod = $this->periodService->getCurrentPeriod($participant);

        if (!$currentPeriod || $currentPeriod->isFulfilled()) {
            return redirect()->route('peserta.dashboard')
                ->with('error', 'Target kehadiran periode ini sudah terpenuhi. ✓');
        }

        // Get the participant's assigned location
        $location = $participant->location()->where('is_active', true)->first();

        return view('peserta.absence', compact('participant', 'currentPeriod', 'location'));
    }

    /**
     * Process the attendance submission.
     *
     * Validation flow:
     * 1. Validate request inputs
     * 2. Run 7-step AttendanceService::validateAbsence()
     * 3. If invalid → recordAttempt() → redirect with error
     * 4. Validate photo (step 8)
     * 5. Store photo on private disk
     * 6. recordAttendance()
     * 7. Redirect to dashboard with success
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'latitude'  => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'accuracy'  => 'nullable|numeric',
            'photo'     => 'required|file|mimes:jpeg,jpg,png|max:5120',
        ], [
            'photo.required' => 'Foto selfie wajib diambil.',
            'photo.mimes'    => 'Format foto harus JPEG atau PNG.',
            'photo.max'      => 'Ukuran foto maksimal 5MB.',
        ]);

        $participant = Auth::user()->participantProfile;

        if (!$participant) {
            return redirect()->route('peserta.dashboard');
        }

        // Parse GPS values
        $lat      = $request->filled('latitude')  ? (float) $request->input('latitude')  : null;
        $lng      = $request->filled('longitude') ? (float) $request->input('longitude') : null;
        $accuracy = $request->filled('accuracy')  ? (float) $request->input('accuracy')  : null;

        // Run 7-step server validation
        $result = $this->attendanceService->validateAbsence($participant, $lat, $lng, $accuracy);

        if (!$result['valid']) {
            // Record failed attempt (if we at least know coordinates or failure reason)
            $this->attendanceService->recordAttempt(
                $participant,
                $lat,
                $lng,
                $accuracy,
                $result['error_message'],
                $result['location'] ?? null,
                $result['distance'] ?? null,
            );

            return back()->withErrors(['attendance' => $result['error_message']]);
        }

        // Step 8: Store the selfie photo on private disk
        $path = $request->file('photo')->store(
            'selfies/' . $participant->id . '/' . today()->format('Y/m'),
            'private'
        );

        // Get current period
        $currentPeriod = $this->periodService->getCurrentPeriod($participant);

        if (!$currentPeriod) {
            return back()->withErrors(['attendance' => 'Tidak ada periode absensi aktif. Hubungi petugas.']);
        }

        // Record successful attendance
        $this->attendanceService->recordAttendance(
            $participant,
            $result['location'],
            $currentPeriod,
            $lat,
            $lng,
            $accuracy ?? 0,
            $path
        );

        return redirect()->route('peserta.dashboard')
            ->with('success', 'Absensi berhasil dicatat! Terima kasih. ✓');
    }
}

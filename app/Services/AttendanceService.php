<?php

namespace App\Services;

use App\Models\AttendanceAttempt;
use App\Models\AttendanceLog;
use App\Models\AttendancePeriod;
use App\Models\Location;
use App\Models\Participant;
use Illuminate\Support\Facades\Request;

class AttendanceService
{
    /**
     * Calculate the great-circle distance between two GPS coordinates
     * using the Haversine formula.
     *
     * @return float  Distance in metres.
     */
    public function haversineDistance(
        float $lat1,
        float $lng1,
        float $lat2,
        float $lng2
    ): float {
        $earthRadius = 6371000; // metres

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2)
           + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
           * sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Get the required location for the participant's NEXT check-in.
     *
     * Each check-in order (1st, 2nd, 3rd...) has a specific assigned location.
     * The order is determined by how many times the participant has already
     * checked in during the current period + 1.
     *
     * @return array{
     *   location: ?Location,
     *   distance: ?float,
     *   within_radius: bool,
     *   check_in_order: int,
     * }
     */
    public function getRequiredLocation(Participant $participant, float $lat, float $lng, int $currentAttendedCount): array
    {
        $nextCheckInOrder = $currentAttendedCount + 1;

        // Get the specific location assigned to this check-in order
        $location = $participant->locations()
            ->wherePivot('check_in_order', $nextCheckInOrder)
            ->where('is_active', true)
            ->first();

        if (!$location) {
            return [
                'location'       => null,
                'distance'       => null,
                'within_radius'  => false,
                'check_in_order' => $nextCheckInOrder,
            ];
        }

        $distance = $this->haversineDistance(
            $lat,
            $lng,
            (float) $location->latitude,
            (float) $location->longitude
        );

        return [
            'location'       => $location,
            'distance'       => $distance,
            'within_radius'  => $distance <= $location->radius_meters,
            'check_in_order' => $nextCheckInOrder,
        ];
    }

    /**
     * Run the 7-step server-side validation for an attendance submission.
     *
     * Step 8 (photo validation) is handled separately in the controller
     * because the file has not yet been stored at this point.
     *
     * @return array{
     *   valid: bool,
     *   error_message: ?string,
     *   error_code: ?string,
     *   location: ?Location,
     *   distance: ?float,
     * }
     */
    public function validateAbsence(
        Participant $participant,
        ?float $lat,
        ?float $lng,
        ?float $accuracy
    ): array {
        // [1] Supervision period must still be active
        if (!$participant->isActive()) {
            return $this->invalid(
                'Masa pengawasan Anda sudah berakhir.',
                'SUPERVISION_ENDED'
            );
        }

        // [2] Must not have already checked in today
        if ($participant->hasAbsentToday()) {
            return $this->invalid(
                'Anda sudah melakukan absensi hari ini.',
                'ALREADY_ABSENT'
            );
        }

        // [3] Quota for current period must not be full
        $currentPeriod = (new PeriodService())->getCurrentPeriod($participant);

        if (!$currentPeriod || $currentPeriod->isFulfilled()) {
            return $this->invalid(
                'Target kehadiran periode ini sudah terpenuhi.',
                'QUOTA_FULL'
            );
        }

        // [4] Daily attempt limit (max 10) must not be exceeded
        $attemptsToday = AttendanceAttempt::where('participant_id', $participant->id)
            ->whereDate('attempted_at', today())
            ->count();

        if ($attemptsToday >= 10) {
            return $this->invalid(
                'Terlalu banyak percobaan. Coba lagi besok atau hubungi petugas.',
                'MAX_ATTEMPTS'
            );
        }

        // [5] GPS coordinates must be present
        if (is_null($lat) || is_null($lng)) {
            return $this->invalid(
                'Gagal mendapatkan lokasi GPS. Pastikan GPS aktif dan izin lokasi diberikan.',
                'NO_GPS'
            );
        }

        // [6] GPS accuracy must be sufficient (≤ 500 m)
        if (!is_null($accuracy) && $accuracy > 500) {
            return $this->invalid(
                'Sinyal GPS terlalu lemah (akurasi ' . round($accuracy) . 'm). Pindah ke area terbuka.',
                'GPS_ACCURACY'
            );
        }

        // [7] Must be within the radius of the SPECIFIC location for this check-in order
        $attendedCount = $currentPeriod->attended_count ?? 0;
        $locationResult = $this->getRequiredLocation($participant, $lat, $lng, $attendedCount);

        if (!$locationResult['location']) {
            return [
                'valid'         => false,
                'error_message' => "Tidak ada lokasi wajib lapor yang ditetapkan untuk absensi ke-{$locationResult['check_in_order']}.",
                'error_code'    => 'NO_LOCATION_ASSIGNED',
                'location'      => null,
                'distance'      => null,
            ];
        }

        if (!$locationResult['within_radius']) {
            $distanceText = $locationResult['distance']
                ? round($locationResult['distance']) . 'm'
                : 'tidak diketahui';
            $locName = $locationResult['location']->name;

            return [
                'valid'         => false,
                'error_message' => "Anda berada di luar area \"{$locName}\" (lokasi untuk absensi ke-{$locationResult['check_in_order']}). Jarak Anda: {$distanceText}.",
                'error_code'    => 'OUT_OF_RANGE',
                'location'      => $locationResult['location'],
                'distance'      => $locationResult['distance'],
            ];
        }

        return [
            'valid'         => true,
            'error_message' => null,
            'error_code'    => null,
            'location'      => $locationResult['location'],
            'distance'      => $locationResult['distance'],
        ];
    }

    /**
     * Save a rejected attendance attempt to the database.
     *
     * Called whenever validateAbsence() returns valid = false
     * (except when the failure is GPS/photo related before coordinates exist).
     */
    public function recordAttempt(
        Participant $participant,
        ?float $lat,
        ?float $lng,
        ?float $accuracy,
        string $failureReason,
        ?Location $location,
        ?float $distance
    ): AttendanceAttempt {
        return AttendanceAttempt::create([
            'participant_id'  => $participant->id,
            'location_id'     => $location?->id,
            'attempted_at'    => now(),
            'latitude'        => $lat,
            'longitude'       => $lng,
            'distance_meters' => $distance ? round($distance, 2) : null,
            'failure_reason'  => $failureReason,
            'metadata'        => $accuracy !== null ? ['accuracy_meters' => $accuracy] : null,
            'ip_address'      => Request::ip(),
            'user_agent'      => Request::userAgent(),
        ]);
    }

    /**
     * Record a successful attendance check-in.
     *
     * Stores the log entry and increments the attended_count on the period.
     */
    public function recordAttendance(
        Participant $participant,
        Location $location,
        AttendancePeriod $period,
        float $lat,
        float $lng,
        float $accuracy,
        string $photoPath
    ): AttendanceLog {
        $log = AttendanceLog::create([
            'participant_id'      => $participant->id,
            'attendance_period_id'=> $period->id,
            'location_id'         => $location->id,
            'attendance_date'     => today()->toDateString(),
            'attendance_time'     => now()->format('H:i:s'),
            'latitude'            => $lat,
            'longitude'           => $lng,
            'distance_meters'     => round(
                $this->haversineDistance(
                    $lat, $lng,
                    (float) $location->latitude,
                    (float) $location->longitude
                ),
                2
            ),
            'photo_path'          => $photoPath,
            'status'              => 'valid',
        ]);

        // Increment denormalized counter on the period
        $period->increment('attended_count');

        // Mark period as completed if quota is now fulfilled
        $period->refresh();
        if ($period->isFulfilled()) {
            $period->update(['status' => 'completed']);
        }

        return $log;
    }

    // -------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------

    /**
     * Build a standard "invalid" result array.
     */
    private function invalid(
        string $message,
        string $code,
        ?Location $location = null,
        ?float $distance = null
    ): array {
        return [
            'valid'         => false,
            'error_message' => $message,
            'error_code'    => $code,
            'location'      => $location,
            'distance'      => $distance,
        ];
    }
}

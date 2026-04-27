<?php

namespace App\Services;

use App\Mail\WarningNotificationMail;
use App\Models\AttendancePeriod;
use App\Models\Participant;
use App\Models\User;
use App\Models\Warning;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class WarningService
{
    /**
     * Scheduler entry point — run through all active participants and
     * generate warnings where thresholds have been crossed.
     *
     * Should be called daily at 08:00 WIB.
     *
     * @return int  Total number of warnings created.
     */
    public function checkAndGenerateWarnings(): int
    {
        // Only participants still within their supervision window
        $participants = Participant::where('status', 'active')
            ->where('supervision_end', '>=', today())
            ->get();

        $total = 0;

        foreach ($participants as $participant) {
            $total += $this->checkParticipantWarning($participant);
        }

        Log::info("WarningService::checkAndGenerateWarnings — {$total} warning(s) created.");

        return $total;
    }

    /**
     * Evaluate all three warning levels for a single participant.
     *
     * @return int  Number of warnings created for this participant (0–3).
     */
    public function checkParticipantWarning(Participant $participant): int
    {
        $created = 0;

        $created += $this->checkLevel1($participant);
        $created += $this->checkLevel2($participant);
        $created += $this->checkLevel3($participant);

        return $created;
    }

    // -------------------------------------------------------
    // Level checks
    // -------------------------------------------------------

    /**
     * Level 1 — Dashboard notification when ≤ 3 days remain in current
     * period and quota is not yet fulfilled.
     */
    private function checkLevel1(Participant $participant): int
    {
        $currentPeriod = (new PeriodService())->getCurrentPeriod($participant);

        if (!$currentPeriod) {
            return 0;
        }

        $remainingDays  = $currentPeriod->getRemainingDays();
        $remainingCount = $currentPeriod->getRemainingCount();

        if ($remainingDays > 3 || $remainingCount <= 0) {
            return 0;
        }

        // Only one level_1 warning per period
        $alreadyExists = Warning::where('participant_id', $participant->id)
            ->where('attendance_period_id', $currentPeriod->id)
            ->where('level', 'level_1')
            ->where('status', 'active')
            ->exists();

        if ($alreadyExists) {
            return 0;
        }

        Warning::create([
            'participant_id'      => $participant->id,
            'attendance_period_id'=> $currentPeriod->id,
            'level'               => 'level_1',
            'reason'              => "Periode wajib lapor akan berakhir dalam {$remainingDays} hari. "
                                   . "Masih ada {$remainingCount} kehadiran yang harus dipenuhi.",
            'issued_at'           => now(),
            'status'              => 'active',
        ]);

        return 1;
    }

    /**
     * Level 2 — Missed quota after period ended.
     * Triggers when a period ended yesterday and quota was not fulfilled.
     * Sends email to assigned admin.
     */
    private function checkLevel2(Participant $participant): int
    {
        $recentEndedPeriod = $participant->attendancePeriods()
            ->where('period_end', today()->subDay()->toDateString())
            ->first();

        if (!$recentEndedPeriod) {
            return 0;
        }

        // If quota was fulfilled, no warning needed
        if ($recentEndedPeriod->isFulfilled()) {
            return 0;
        }

        // Only one level_2 warning per period (no unique constraint bypass)
        $alreadyExists = Warning::where('participant_id', $participant->id)
            ->where('attendance_period_id', $recentEndedPeriod->id)
            ->where('level', 'level_2')
            ->exists();

        if ($alreadyExists) {
            return 0;
        }

        $missing = $recentEndedPeriod->getRemainingCount();

        Warning::create([
            'participant_id'      => $participant->id,
            'attendance_period_id'=> $recentEndedPeriod->id,
            'level'               => 'level_2',
            'reason'              => "Periode wajib lapor telah berakhir dengan {$missing} kehadiran yang tidak terpenuhi.",
            'issued_at'           => now(),
            'status'              => 'active',
        ]);

        // Send email to assigned admin
        $this->sendWarningEmail($participant, 2);

        return 1;
    }

    /**
     * Level 3 — Critical escalation when participant has ≥ 2 unresolved
     * Level 2 warnings. Sends email to ALL admins.
     */
    private function checkLevel3(Participant $participant): int
    {
        $level2UnresolvedCount = Warning::where('participant_id', $participant->id)
            ->where('level', 'level_2')
            ->where('status', 'active')
            ->count();

        if ($level2UnresolvedCount < 2) {
            return 0;
        }

        // Only one active level_3 warning at a time
        $alreadyExists = Warning::where('participant_id', $participant->id)
            ->where('level', 'level_3')
            ->where('status', 'active')
            ->exists();

        if ($alreadyExists) {
            return 0;
        }

        // Link to the latest period for reference
        $latestPeriod = $participant->attendancePeriods()->latest('period_end')->first();

        Warning::create([
            'participant_id'      => $participant->id,
            'attendance_period_id'=> $latestPeriod?->id,
            'level'               => 'level_3',
            'reason'              => 'Peserta telah mangkir pada 2 periode berturut-turut. '
                                   . 'Peserta wajib hadir langsung ke Polres.',
            'issued_at'           => now(),
            'status'              => 'active',
        ]);

        // Send email to all admins
        $this->sendWarningEmail($participant, 3);

        return 1;
    }

    // -------------------------------------------------------
    // Email
    // -------------------------------------------------------

    /**
     * Send warning notification email.
     *
     * Level 2 → assigned admin only.
     * Level 3 → all admins (broadcast).
     */
    public function sendWarningEmail(Participant $participant, int $level): void
    {
        try {
            if ($level === 2) {
                $assignedAdmin = $participant->assignedAdmin;

                if (!$assignedAdmin || !$assignedAdmin->email) {
                    Log::warning("WarningService: Assigned admin not found or has no email for participant #{$participant->id}");
                    return;
                }

                Mail::to($assignedAdmin->email)
                    ->send(new WarningNotificationMail($participant, $level));

            } elseif ($level === 3) {
                $admins = User::where('role', 'admin')
                    ->whereNotNull('email')
                    ->get();

                if ($admins->isEmpty()) {
                    Log::warning('WarningService: No admin accounts with email found for Level 3 broadcast.');
                    return;
                }

                Mail::to($admins->pluck('email')->toArray())
                    ->send(new WarningNotificationMail($participant, $level));
            }
        } catch (\Throwable $e) {
            // Log but don't crash — warning is already saved to DB
            Log::error("WarningService: Failed to send level-{$level} email for participant #{$participant->id}: {$e->getMessage()}");
        }
    }
}

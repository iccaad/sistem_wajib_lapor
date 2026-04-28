<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use App\Models\AttendancePeriod;
use App\Models\Participant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Override manual absensi oleh admin.
     * Kondisi:
     * - Tanggal yang dipilih harus punya periode aktif
     * - Belum ada absensi di tanggal tersebut
     * - Kuota periode belum terpenuhi
     */
    public function override(Request $request, Participant $participant): RedirectResponse
    {
        $request->validate([
            'attendance_date'  => 'required|date|before_or_equal:today',
            'override_reason'  => 'required|string|min:10|max:1000',
        ]);

        $date = $request->date('attendance_date');

        // Cek apakah sudah ada absensi di tanggal ini
        $alreadyExists = AttendanceLog::where('participant_id', $participant->id)
            ->where('attendance_date', $date->toDateString())
            ->exists();

        if ($alreadyExists) {
            return back()->withErrors(['override' => 'Peserta sudah memiliki catatan absensi pada tanggal tersebut.']);
        }

        // Cari periode yang mencakup tanggal ini
        $period = AttendancePeriod::where('participant_id', $participant->id)
            ->where('period_start', '<=', $date)
            ->where('period_end', '>=', $date)
            ->first();

        if (!$period) {
            return back()->withErrors(['override' => 'Tidak ada periode aktif yang mencakup tanggal tersebut.']);
        }

        // Cek kuota
        if ($period->isFulfilled()) {
            return back()->withErrors(['override' => 'Kuota periode ini sudah terpenuhi. Absensi manual tidak dapat ditambahkan.']);
        }

        AttendanceLog::create([
            'participant_id'       => $participant->id,
            'attendance_period_id' => $period->id,
            'location_id'          => null,
            'attendance_date'      => $date->toDateString(),
            'attendance_time'      => now()->format('H:i:s'),
            'latitude'             => 0,
            'longitude'            => 0,
            'photo_path'           => null,
            'notes'                => 'Override manual: ' . $request->override_reason,
            'status'               => 'manual_override',
        ]);

        // Increment period attended count
        $period->increment('attended_count');
        $period->refresh();
        if ($period->isFulfilled()) {
            $period->update(['status' => 'completed']);
        }

        return back()->with('success', 'Absensi manual berhasil ditambahkan untuk tanggal ' . $date->translatedFormat('d F Y') . '.');
    }

    /**
     * Serve selfie photo securely (private disk, admin-only).
     */
    public function showPhoto(AttendanceLog $log): BinaryFileResponse
    {
        abort_if(!$log->photo_path, 404, 'Foto tidak tersedia.');

        $path = storage_path('app/private/' . $log->photo_path);

        abort_if(!file_exists($path), 404, 'File foto tidak ditemukan.');

        $mimeType = mime_content_type($path) ?: 'image/jpeg';

        return response()->file($path, [
            'Content-Type'        => $mimeType,
            'Content-Disposition' => 'inline; filename="selfie-' . $log->id . '.jpg"',
            'Cache-Control'       => 'private, no-store',
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait FiltersParticipants
{
    /**
     * Apply the 3 dynamic stat filters to a Participant query.
     *
     * - status_akun:          Filters the `status` column (active / inactive).
     * - tingkat_kepatuhan:    Patuh = no active warnings, Berisiko = active level_1,
     *                         Mangkir = active level_2 or level_3.
     * - progress_pengawasan:  Selesai = supervision_end < today,
     *                         Segera Selesai = supervision_end within 7 days.
     */
    protected function applyParticipantFilters(Builder $query, Request $request): Builder
    {
        // ── Status Akun ──
        $query->when($request->input('status_akun'), function (Builder $q, string $status) {
            if ($status === 'aktif') {
                $q->where('participants.status', 'active');
            } elseif ($status === 'tidak_aktif') {
                $q->where('participants.status', 'inactive');
            }
        });

        // ── Tingkat Kepatuhan ──
        // Only meaningful for active participants. When status_akun = tidak_aktif,
        // skip compliance filters to avoid blank-query conflicts.
        $query->when(
            $request->input('tingkat_kepatuhan') && $request->input('status_akun') !== 'tidak_aktif',
            function (Builder $q) use ($request) {
                $kepatuhan = $request->input('tingkat_kepatuhan');

                if ($kepatuhan === 'patuh') {
                    // No active warnings at all
                    $q->whereDoesntHave('warnings', fn (Builder $w) => $w->where('status', 'active'));
                } elseif ($kepatuhan === 'berisiko') {
                    // Has at least one active Warning Level 1
                    $q->whereHas('warnings', fn (Builder $w) => $w->where('status', 'active')->where('level', 'level_1'));
                } elseif ($kepatuhan === 'mangkir') {
                    // Has at least one active Warning Level 2 or 3
                    $q->whereHas('warnings', fn (Builder $w) => $w->where('status', 'active')->whereIn('level', ['level_2', 'level_3']));
                }
            }
        );

        // ── Progress Pengawasan ──
        $query->when(
            $request->input('progress_pengawasan') && $request->input('status_akun') !== 'tidak_aktif',
            function (Builder $q) use ($request) {
                $progress = $request->input('progress_pengawasan');

                if ($progress === 'selesai') {
                    // supervision_end is in the past
                    $q->where('participants.supervision_end', '<', today());
                } elseif ($progress === 'segera_selesai') {
                    // supervision_end is within the next 7 days (today..+7)
                    $q->whereBetween('participants.supervision_end', [today(), today()->addDays(7)]);
                }
            }
        );

        return $query;
    }
}

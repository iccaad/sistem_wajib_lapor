<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ParticipantsReportExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    protected $participants;

    public function __construct(Collection $participants)
    {
        $this->participants = $participants;
    }

    public function collection()
    {
        return $this->participants;
    }

    public function headings(): array
    {
        return [
            'Nama',
            'NIK',
            'Polsek/Satuan yang bertanggung jawab',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Jumlah Periode',
            'Kuota per Periode',
            'Total Absen pada Tiap Periode',
        ];
    }

    public function map($participant): array
    {
        $attendanceStrings = [];

        foreach ($participant->attendancePeriods as $period) {
            $attendanceStrings[] = $period->attended_count.'/Minggu';
        }

        $totalAbsen = implode(', ', $attendanceStrings);
        if (empty($totalAbsen)) {
            $totalAbsen = '—';
        }

        return [
            $participant->full_name,
            "'".$participant->nik, // Prefix with apostrophe to prevent excel from treating NIK as number
            $participant->assignedAdmin ? $participant->assignedAdmin->name : '—',
            $participant->supervision_start->format('d/m/Y'),
            $participant->supervision_end->format('d/m/Y'),
            $participant->attendancePeriods->count(),
            $participant->quota_amount.'×/'.($participant->quota_type === 'weekly' ? 'minggu' : 'bulan'),
            $totalAbsen,
        ];
    }
}

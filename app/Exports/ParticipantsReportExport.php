<?php

namespace App\Exports;

use App\Models\Participant;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Collection;

class ParticipantsReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $participants;

    public function __construct(Collection $participants)
    {
        $this->participants = $participants;
    }

    public function collection()
    {
        $rows = collect([]);

        foreach ($this->participants as $participant) {
            if ($participant->attendancePeriods->isEmpty()) {
                $rows->push((object)[
                    'participant' => $participant,
                    'period' => null
                ]);
            } else {
                foreach ($participant->attendancePeriods as $period) {
                    $rows->push((object)[
                        'participant' => $participant,
                        'period' => $period
                    ]);
                }
            }
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Nama',
            'NIK',
            'Polsek/Satuan yang Bertanggung Jawab',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Jumlah Periode',
            'Kuota per Periode',
            'Rentang Periode',
            'Total Absen pada Tiap Periode'
        ];
    }

    public function map($row): array
    {
        $p = $row->participant;
        $period = $row->period;

        return [
            $p->full_name,
            "'" . $p->nik, // Prefix with apostrophe to prevent excel from treating NIK as number
            $p->assignedAdmin ? $p->assignedAdmin->name : '—',
            $p->supervision_start->format('d/m/Y'),
            $p->supervision_end->format('d/m/Y'),
            $p->attendancePeriods->count(),
            $p->quota_amount . '×/' . ($p->quota_type === 'weekly' ? 'minggu' : 'bulan'),
            $period ? $period->period_start->format('d/m/Y') . ' - ' . $period->period_end->format('d/m/Y') : '—',
            $period ? $period->attended_count : '0',
        ];
    }
}

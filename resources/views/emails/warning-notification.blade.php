<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi Peringatan — Sistem Wajib Lapor</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f5f5f5;
            color: #333333;
            padding: 24px 16px;
        }

        .wrapper {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        /* ── Header ── */
        .header {
            padding: 28px 32px;
            text-align: center;
        }
        .header.level-2 { background-color: #dc2626; }
        .header.level-3 { background-color: #7f1d1d; }

        .header h1 {
            color: #ffffff;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .header p {
            color: rgba(255,255,255,0.85);
            font-size: 13px;
            margin-top: 6px;
        }

        /* ── Badge ── */
        .badge-wrapper {
            text-align: center;
            margin: 24px 0 8px;
        }
        .badge {
            display: inline-block;
            padding: 6px 18px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }
        .badge.level-2 { background: #fee2e2; color: #b91c1c; }
        .badge.level-3 { background: #7f1d1d; color: #fecaca; }

        /* ── Body ── */
        .body {
            padding: 24px 32px 32px;
        }

        .intro {
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 20px;
            color: #555;
        }

        /* ── Info card ── */
        .info-card {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 20px;
        }
        .info-card-title {
            background: #f9fafb;
            padding: 10px 16px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #6b7280;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-row {
            display: flex;
            padding: 10px 16px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 13px;
        }
        .info-row:last-child { border-bottom: none; }
        .info-label {
            width: 180px;
            flex-shrink: 0;
            color: #6b7280;
            font-weight: 500;
        }
        .info-value { color: #111827; font-weight: 600; }

        /* ── Warning box ── */
        .warning-box {
            border-radius: 6px;
            padding: 14px 16px;
            margin-bottom: 20px;
            font-size: 13px;
            line-height: 1.6;
        }
        .warning-box.level-2 {
            background: #fef2f2;
            border-left: 4px solid #dc2626;
            color: #991b1b;
        }
        .warning-box.level-3 {
            background: #fef2f2;
            border-left: 4px solid #7f1d1d;
            color: #7f1d1d;
        }
        .warning-box strong { display: block; margin-bottom: 4px; }

        /* ── CTA Button ── */
        .cta-wrapper { text-align: center; margin: 24px 0 8px; }
        .cta-button {
            display: inline-block;
            padding: 12px 28px;
            background-color: #1d4ed8;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
        }

        /* ── Footer ── */
        .footer {
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
            padding: 16px 32px;
            text-align: center;
            font-size: 11px;
            color: #9ca3af;
        }
    </style>
</head>
<body>
<div class="wrapper">

    {{-- ── Header ── --}}
    <div class="header level-{{ $level }}">
        <h1>
            @if($level === 3) 🚨 @else ⚠️ @endif
            Sistem Wajib Lapor — Polrestabes Semarang
        </h1>
        <p>Notifikasi Peringatan Kepatuhan Peserta</p>
    </div>

    {{-- ── Badge ── --}}
    <div class="badge-wrapper">
        <span class="badge level-{{ $level }}">
            @if($level === 2)
                Peringatan Level 2 — Mangkir Periode
            @else
                Peringatan Level 3 — Eskalasi Kritis
            @endif
        </span>
    </div>

    <div class="body">

        {{-- ── Intro ── --}}
        <p class="intro">
            @if($level === 2)
                Peserta berikut <strong>tidak memenuhi kuota kehadiran</strong> pada periode yang baru berakhir.
                Harap tindak lanjuti sesuai prosedur yang berlaku.
            @else
                Peserta berikut telah <strong>mangkir pada 2 periode berturut-turut</strong> dan kini dalam
                status eskalasi kritis. Peserta <strong>wajib hadir langsung ke Polres</strong>.
                Seluruh admin diberitahukan.
            @endif
        </p>

        {{-- ── Participant info ── --}}
        <div class="info-card">
            <div class="info-card-title">Data Peserta</div>

            <div class="info-row">
                <span class="info-label">Nama Lengkap</span>
                <span class="info-value">{{ $participant->full_name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">NIK</span>
                <span class="info-value">{{ $participant->nik }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Jenis Pelanggaran</span>
                <span class="info-value">{{ $participant->violationType->name ?? '—' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Masa Pengawasan</span>
                <span class="info-value">
                    {{ \Carbon\Carbon::parse($participant->supervision_start)->translatedFormat('d M Y') }}
                    s.d.
                    {{ \Carbon\Carbon::parse($participant->supervision_end)->translatedFormat('d M Y') }}
                </span>
            </div>
            @if($participant->phone)
            <div class="info-row">
                <span class="info-label">No. Telepon</span>
                <span class="info-value">{{ $participant->phone }}</span>
            </div>
            @endif
            @if($participant->assignedAdmin)
            <div class="info-row">
                <span class="info-label">Admin Penanggungjawab</span>
                <span class="info-value">{{ $participant->assignedAdmin->name }}</span>
            </div>
            @endif
        </div>

        {{-- ── Warning message ── --}}
        @php
            $latestWarning = $participant->warnings()
                ->where('level', 'level_' . $level)
                ->latest('issued_at')
                ->first();
        @endphp
        @if($latestWarning)
        <div class="warning-box level-{{ $level }}">
            <strong>Keterangan Peringatan:</strong>
            {{ $latestWarning->reason }}
        </div>
        @endif

        {{-- ── CTA ── --}}
        <div class="cta-wrapper">
            <a href="{{ $adminPanelUrl }}" class="cta-button">
                Lihat Detail di Panel Admin →
            </a>
        </div>

    </div>

    {{-- ── Footer ── --}}
    <div class="footer">
        <p>Email ini dikirim otomatis oleh <strong>Sistem Wajib Lapor Digital</strong>.</p>
        <p>Polrestabes Semarang &nbsp;|&nbsp; {{ now()->translatedFormat('d M Y, H:i') }} WIB</p>
    </div>

</div>
</body>
</html>

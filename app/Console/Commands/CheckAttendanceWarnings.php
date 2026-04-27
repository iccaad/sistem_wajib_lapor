<?php

namespace App\Console\Commands;

use App\Services\WarningService;
use Illuminate\Console\Command;

class CheckAttendanceWarnings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:check-warnings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cek kepatuhan peserta dan generate peringatan jika perlu';

    /**
     * Execute the console command.
     */
    public function handle(WarningService $warningService): int
    {
        $this->info('[' . now()->format('Y-m-d H:i:s') . '] attendance:check-warnings — Mulai...');

        $generated = $warningService->checkAndGenerateWarnings();

        if ($generated === 0) {
            $this->line('  → Tidak ada peringatan baru yang dibuat.');
        } else {
            $this->info("  → Generated {$generated} warning(s).");
        }

        $this->info('Selesai.');

        return Command::SUCCESS;
    }
}

<?php

namespace App\Console\Commands;

use App\Services\PeriodService;
use Illuminate\Console\Command;

class GenerateNextPeriods extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'periods:generate-next';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate periode berikutnya untuk peserta yang periodenya baru selesai';

    /**
     * Execute the console command.
     */
    public function handle(PeriodService $periodService): int
    {
        $this->info('['.now()->format('Y-m-d H:i:s').'] periods:generate-next — Mulai...');

        $updated = $periodService->updateExpiredPeriodsStatus();

        if ($updated === 0) {
            $this->line('  → Tidak ada periode kedaluwarsa yang perlu diperbarui.');
        } else {
            $this->info("  → Diperbarui {$updated} periode yang kedaluwarsa.");
        }

        $this->info('Selesai.');

        return Command::SUCCESS;
    }
}

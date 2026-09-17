<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('saweria:sync --prune')
            ->everyFifteenMinutes()
            ->withoutOverlapping(20)
            ->appendOutputTo(storage_path('logs/saweria-sync.log'));

        // Dijadwalkan lebih rapat daripada masa simpannya (60 menit) supaya
        // pengunjung tidak pernah menanggung waktu tunggu pengambilan data.
        $schedule->command('gamestats:refresh')
            ->everyThirtyMinutes()
            ->withoutOverlapping(10)
            ->appendOutputTo(storage_path('logs/gamestats.log'));

        // Menuntaskan pesanan yang detailnya belum terambil saat callback tiba,
        // sekaligus memperbarui status pesanan yang belum selesai.
        $schedule->command('saweria:sync-orders')
            ->everyFiveMinutes()
            ->withoutOverlapping(10)
            ->appendOutputTo(storage_path('logs/saweria-orders.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

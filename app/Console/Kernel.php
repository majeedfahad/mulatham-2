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
        // Send daily Telegram report at 11 PM (Saudi time is UTC+3)
        $schedule->command('telegram:daily-report')->dailyAt('23:00')->timezone('Asia/Riyadh');

        // Cleanup idle rooms every hour
        $schedule->command('rooms:cleanup-idle')->hourly();
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

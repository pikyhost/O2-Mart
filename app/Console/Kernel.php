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
        // Sync Jeebly order statuses every 10 minutes
        $schedule->command('jeebly:sync-status --limit=100')
                 ->everyTenMinutes()
                 ->withoutOverlapping();

        // Test cron job is working
        $schedule->call(function () {
            \Log::info('✅ Cron job is working at ' . now());
        })->everyMinute();

        // restart any old worker every 5 min
        $schedule->command('queue:restart')->everyFiveMinutes();

        // run a continuous worker every minute if not running
        $schedule->command('/usr/bin/php83 /home/tohf/public_html/o2mart/artisan queue:work --tries=3 --timeout=90')
                 ->everyMinute()
                 ->withoutOverlapping()
                 ->runInBackground(); 
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
}

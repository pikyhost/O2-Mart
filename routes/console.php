<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

//Artisan::command('inspire', function () {
//    $this->comment(Inspiring::quote());
//})->purpose('Display an inspiring quote');


//Artisan::command('test:log-cron', function () {
//    Log::info('✅ test:log-cron ran at ' . now());
//})->purpose('Log to test if cron is running')
//    ->everyMinute() // Run every minute
//    ->withoutOverlapping()
//    ->appendOutputTo(storage_path('logs/schedule.log'));

Artisan::command('queue:process', function () {
    $this->info('Starting queue worker...');

    Artisan::call('queue:work', [
        '--stop-when-empty' => true,
        '--tries' => 3,
    ]);

    $this->info('Queue worker finished!');
})->purpose('Process the queue until empty');

Artisan::command('queue:start', function () {
    $this->info('Starting continuous queue worker...');
    
    exec('/usr/bin/php83 /home/tohf/public_html/o2mart/artisan queue:work --tries=3 --timeout=90 > /dev/null 2>&1 &');
    
    $this->info('Queue worker started in background!');
})->purpose('Start continuous queue worker for production');

// info done.

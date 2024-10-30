<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote')->hourly();

// Schedule::command('app:fetch-guardian-a-p-i-articles')->everySixHours();
// Schedule::command('app:fetch-news-a-p-i-articles')->everySixHours();
// Schedule::command('fetch:fetch-n-y-t-articles')->everySixHours();
Schedule::command('app:fetch-guardian-a-p-i-articles')->everyMinute();
Schedule::command('app:fetch-news-a-p-i-articles')->everyMinute();
Schedule::command('fetch:fetch-n-y-t-articles')->everyMinute();

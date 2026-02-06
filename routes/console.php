<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\SendTrackerReport;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('tracker:send-report')
        ->twiceDaily(8, 17) // Runs at 8:00 AM and 5:00 PM
        ->timezone('Africa/Nairobi'); // Ensures it runs at Kenya time
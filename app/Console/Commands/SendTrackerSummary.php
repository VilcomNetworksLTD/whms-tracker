<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendTrackerSummaryEmail;

class SendTrackerSummary extends Command
{
    protected $signature = 'tracker:summary';
    protected $description = 'Send tracker summary emails to all users immediately';

    public function handle()
    {
        // Run the job immediately in the same process
        SendTrackerSummaryEmail::dispatchSync();

        $this->info('Tracker summary job ran successfully.');
    }
}


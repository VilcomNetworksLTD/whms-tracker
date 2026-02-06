<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TrackerForm;
use App\Mail\DailyTrackerReport;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendTrackerReport extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tracker:send-report';

    /**
     * The console command description.
     */
    protected $description = 'Sends a twice-daily report of monthly tracker data';

    /**
     * Execute the console command.
     */
  public function handle()
{
    $this->info('Generating monthly cumulative report...');

    // 1. Set Timezone to Nairobi (Important for accurate "Start of Month")
    $timezone = 'Africa/Nairobi';
    
    // 2. Define Range: From 1st of THIS month -> NOW
    $startOfMonth = \Carbon\Carbon::now($timezone)->startOfMonth();
    $endOfMonth   = \Carbon\Carbon::now($timezone)->endOfMonth();

    // 3. Fetch Data (Cumulative for the whole month)
    // We removed 'limit(50)' so you get the FULL report.
    $forms = \App\Models\TrackerForm::whereBetween('date', [$startOfMonth, $endOfMonth])
                                    ->orderBy('date', 'desc')
                                    ->orderBy('created_at', 'desc')
                                    ->get();

    if ($forms->isEmpty()) {
        $this->info('No forms found for this month ('.$startOfMonth->format('M Y').'). Skipping email.');
        return;
    }

    // 4. Calculate Cumulative Stats for the Month
    $stats = [
        'count'      => $forms->count(),
        'amount_in'  => $forms->sum('amount_in'),
        'fees'       => $forms->sum('fees'),
        'amount_out' => $forms->sum('amount_out'),
    ];

    // 5. Define Recipients
    $recipients = ['caleb.kipchirchi@vilcom.co.ke']; 

    // 6. Send Email
    foreach ($recipients as $email) {
        $this->info("Sending report to $email...");
        try {
            \Illuminate\Support\Facades\Mail::to($email)
                ->send(new \App\Mail\DailyTrackerReport($forms, $stats));
            $this->info("✔ Sent successfully to $email");
        } catch (\Exception $e) {
            $this->error("✘ Failed to send to $email: " . $e->getMessage());
        }
    }

    $this->info('All operations completed.');
}
}
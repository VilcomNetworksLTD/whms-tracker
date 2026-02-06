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
        $this->info('Gathering data for the current month...');

        // 1. Get Data for Current Month
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $forms = TrackerForm::whereBetween('date', [$startOfMonth, $endOfMonth])
                            ->orderBy('date', 'desc')
                            ->orderBy('created_at', 'desc')
                            ->limit(50) // Limit to avoid email size limits
                            ->get();

        // 2. Calculate Stats
        $stats = [
            'count' => $forms->count(),
            'amount_in' => $forms->sum('amount_in'),
            'completed' => $forms->whereNotNull('feedback')->count(),
        ];

        // 3. Define Recipients (Comma separated list)
        // You can also move this to .env file: RECIPIENT_EMAILS="admin@vilcom.co.ke,manager@vilcom.co.ke"
        $recipients = ['admin@vilcom.co.ke', 'caleb.kipchirchi@vilcom.co.ke']; 

        // 4. Send Email
        foreach ($recipients as $email) {
            $this->info("Sending to $email...");
            try {
                Mail::to($email)->send(new DailyTrackerReport($forms, $stats));
            } catch (\Exception $e) {
                $this->error("Failed to send to $email: " . $e->getMessage());
            }
        }

        $this->info('Reports sent successfully!');
    }
}
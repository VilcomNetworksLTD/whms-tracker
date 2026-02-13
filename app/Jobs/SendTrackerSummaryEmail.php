<?php

namespace App\Jobs;

use App\Mail\TrackerSummaryMail;
use App\Models\TrackerForm;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendTrackerSummaryEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

   public function handle(): void
{
    try {
        
        $yesterdayStart = now()->subDay()->startOfDay();
        $yesterdayEnd   = now()->subDay()->endOfDay();

        $sentCount = 0;
        $failedCount = 0;

        // Send to all users who have email
        User::whereNotNull('email')->where('email', '!=', '')->chunk(100, function ($users) use ($yesterdayStart, $yesterdayEnd, &$sentCount, &$failedCount) {
            foreach ($users as $user) {
                try {
                    // Fetch **only this user's** tracker entries for yesterday
                    $trackers = TrackerForm::orderBy('created_at', 'desc')->get();

                    //  $trackers = TrackerForm::where('id', $user->id)
                    //     ->whereBetween('created_at', [$yesterdayStart, $yesterdayEnd])
                    //     ->orderBy('created_at', 'desc')
                    //     ->get();

                    Log::info('Sending tracker summary to: '.$user->email.' with '.$trackers->count().' trackers');

                    // Send the email, even if $trackers is empty
                    Mail::to($user->email)
                        ->send(new TrackerSummaryMail($user, $trackers));

                    $sentCount++;
                    Log::info('Successfully sent to: '.$user->email);

                } catch (\Exception $e) {
                    $failedCount++;
                    Log::error('Failed to send email to '.$user->email.': '.$e->getMessage());
                }
            }
        });

        Log::info('Tracker summary email job completed. Sent: '.$sentCount.', Failed: '.$failedCount);

    } catch (\Exception $e) {
        Log::error('SendTrackerSummaryEmail job failed: '.$e->getMessage());
        throw $e;
    }
}

}

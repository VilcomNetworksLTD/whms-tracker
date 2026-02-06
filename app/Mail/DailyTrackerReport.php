<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DailyTrackerReport extends Mailable
{
    use Queueable, SerializesModels;

    public $forms;
    public $stats;
    public $monthName;

    /**
     * Create a new message instance.
     */
    public function __construct($forms, $stats)
    {
        $this->forms = $forms;
        $this->stats = $stats;
        $this->monthName = now()->format('F Y');
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject("Tracker Report: {$this->monthName} (Update)")
                    ->view('emails.daily_report');
    }
}
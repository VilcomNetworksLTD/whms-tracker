<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class TrackerSummaryMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $trackers;
    public $date; // ✅ ADD THIS

    /**
     * Create a new message instance.
     */
    public function __construct($user, $trackers, $date = null) // ✅ ADD DATE PARAMETER
    {
        $this->user = $user;
        $this->trackers = $trackers;
        $this->date = $date ?? Carbon::yesterday(); // ✅ DEFAULT TO YESTERDAY IF NOT PROVIDED
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Daily Tracker Summary - ' . Carbon::parse($this->date)->format('F j, Y'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.tracker-summary', // ✅ USE view() NOT markdown()
            with: [
                'user' => $this->user,
                'trackers' => $this->trackers,
                'date' => $this->date, // ✅ PASS DATE TO VIEW
            ]
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
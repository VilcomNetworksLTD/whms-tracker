<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otpCode; // 1. Define the property

    /**
     * Create a new message instance.
     */
    public function __construct($code) // 2. Accept the code here
    {
        $this->otpCode = $code;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Verification Code',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.otp', // 3. Ensure this matches your blade file name
            with: ['otpCode' => $this->otpCode] // 4. Pass data to view
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
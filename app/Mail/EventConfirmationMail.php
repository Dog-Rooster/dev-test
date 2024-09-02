<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $icsContent;
    protected $icsFileName;

    /**
     * Create a new message instance.
     */
    public function __construct($icsContent, $icsFileName)
    {
        //
        $this->icsContent = $icsContent;
        $this->icsFileName = $icsFileName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Event Confirmation Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.event_confirmation',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->icsContent, $this->icsFileName)
                ->withMime('text/calendar'),
        ];
    }

}

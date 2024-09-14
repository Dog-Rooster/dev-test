<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CalendarInvite extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $icsContent;
    public $subject;
    /**
     * Create a new message instance.
     */
    public function __construct($icsContent, $subject)
    {
        $this->icsContent = $icsContent;
        $this->subject = $subject;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    // public function content(): Content
    // {
    //     return new Content(
    //         view: 'view.name',
    //     );
    // }

    // /**
    //  * Get the attachments for the message.
    //  *
    //  * @return array<int, \Illuminate\Mail\Mailables\Attachment>
    //  */
    // public function attachments(): array
    // {
    //     return [];
    // }

    public function build()
    {
        return $this->view('emails.invite') // Update to your actual view
            ->subject($this->subject)
            ->attachData($this->icsContent, 'invite.ics', [
                'mime' => 'text/calendar',
            ]);
    }
}

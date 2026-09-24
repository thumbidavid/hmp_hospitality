<?php

namespace App\Mail;

use App\Models\RfpSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewRfpSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public RfpSubmission $rfp) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "New RFP Submission: {$this->rfp->reference_number} - {$this->rfp->full_name}",
            replyTo: [
                new Address($this->rfp->email, $this->rfp->full_name),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.rfp.admin-notification',
        );
    }

    /**
     * Attach the uploaded brief directly to the email
     */
    public function attachments(): array
    {
        // If the RFP has an attachment URL (Cloudflare R2 or Storage)
        if (!empty($this->rfp->attachment_url)) {
            return [
                Attachment::fromUrl($this->rfp->attachment_url)
                    ->as('Client_Brief_' . $this->rfp->reference_number . '.pdf'),
            ];
        }

        return [];
    }
}

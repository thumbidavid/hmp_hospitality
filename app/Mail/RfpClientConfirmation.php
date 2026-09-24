<?php

namespace App\Mail;

use App\Models\RfpSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RfpClientConfirmation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public RfpSubmission $rfp) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address', 'hello@hmphospitality.co'), 'HMP Hospitality'),
            subject: "We have received your RFP ({$this->rfp->reference_number}) - HMP Hospitality",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.rfp.client-confirmation',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

<?php

namespace App\Mail;

use App\Models\RfpSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewRfpSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $rfp;

    /**
     * Create a new message instance.
     */
    public function __construct(RfpSubmission $rfp)
    {
        $this->rfp = $rfp;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject("New RFP Submitted [{$this->rfp->reference_number}]")
            ->view('emails.new-rfp')
            ->with([
                'rfp' => $this->rfp,
            ]);
    }
}

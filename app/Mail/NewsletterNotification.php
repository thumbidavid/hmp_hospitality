<?php

namespace App\Mail;

use App\Models\NewsletterSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class NewsletterNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $subscriber;
    public $title;
    public $excerpt;
    public $imageUrl;
    public $targetUrl;
    public $unsubscribeUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(NewsletterSubscriber $subscriber, $title, $excerpt, $imageUrl, $targetUrl)
    {
        $this->subscriber = $subscriber;
        $this->title = $title;
        $this->excerpt = $excerpt;
        $this->imageUrl = $imageUrl;
        $this->targetUrl = $targetUrl;

        // Generate a secure, signed URL specifically for this subscriber
        $this->unsubscribeUrl = URL::signedRoute('newsletter.unsubscribe', [
            'email' => $subscriber->email
        ]);
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject("New from HMP Hospitality: {$this->title}")
            ->view('emails.newsletter')
            ->with([
                'title' => $this->title,
                'excerpt' => $this->excerpt,
                'imageUrl' => $this->imageUrl,
                'targetUrl' => $this->targetUrl,
                'unsubscribeUrl' => $this->unsubscribeUrl,
            ]);
    }
}

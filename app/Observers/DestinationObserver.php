<?php

namespace App\Observers;

use App\Models\Destination;
use App\Models\NewsletterSubscriber;
use App\Mail\NewsletterNotification;
use Illuminate\Support\Facades\Mail;

class DestinationObserver
{
    /**
     * Handle the Destination "created" event.
     */
    public function created(Destination $destination): void
    {
        // 1. Only notify subscribers if the destination is published active immediately
        if (!$destination->is_active) {
            return;
        }

        // 2. Fetch all active newsletter subscribers
        $subscribers = NewsletterSubscriber::where('is_active', true)->get();

        // 3. Queue emails asynchronously for background delivery
        foreach ($subscribers as $subscriber) {
            Mail::to($subscriber->email)->queue(new NewsletterNotification(
                $subscriber,
                "New Destination represented: {$destination->name}",
                $destination->intro_description ?? "Discover new venues, transportation networks, and incentive opportunities in this region.",
                $destination->hero_image_url,
                route('destination.show', $destination->slug) // Direct link to /destinations/{slug}
            ));
        }
    }
}

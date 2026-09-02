<?php

namespace App\Observers;

use App\Models\Property;
use App\Models\NewsletterSubscriber;
use App\Mail\NewsletterNotification;
use Illuminate\Support\Facades\Mail;

class PropertyObserver
{
    /**
     * Handle the Property "created" event.
     */
    public function created(Property $property): void
    {
        // 1. Only notify subscribers if the property is published active immediately
        if (!$property->is_active) {
            return;
        }

        // 2. Fetch all active newsletter subscribers
        $subscribers = NewsletterSubscriber::where('is_active', true)->get();

        // 3. Queue emails asynchronously for background delivery
        foreach ($subscribers as $subscriber) {
            Mail::to($subscriber->email)->queue(new NewsletterNotification(
                $subscriber,
                "New Member: {$property->name}",
                $property->tagline ?? $property->overview ?? "Discover a newly represented independent property in our collection.",
                $property->featured_image_url,
                route('portfolio.show', $property->slug) // Direct link to /portfolio/{slug}
            ));
        }
    }
}

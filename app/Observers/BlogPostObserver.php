<?php

namespace App\Observers;

use App\Models\BlogPost;
use App\Models\NewsletterSubscriber;
use App\Mail\NewsletterNotification;
use Illuminate\Support\Facades\Mail;

class BlogPostObserver
{
    /**
     * Handle the BlogPost "created" event.
     */
    public function created(BlogPost $blogPost): void
    {
        // Only notify if the post is set to active immediately
        if (!$blogPost->is_active) {
            return;
        }

        $subscribers = NewsletterSubscriber::where('is_active', true)->get();

        foreach ($subscribers as $subscriber) {
            // Queue each email for background delivery
            Mail::to($subscriber->email)->queue(new NewsletterNotification(
                $subscriber,
                $blogPost->title,
                $blogPost->excerpt ?? 'Read our latest editorial story from the collection.',
                $blogPost->featured_image_url,
                route('stories.show', $blogPost->slug)
            ));
        }
    }
}

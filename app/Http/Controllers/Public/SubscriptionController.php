<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubscriptionController extends Controller
{
    /**
     * Handle the secure signed unsubscribe request.
     */
    public function unsubscribe(Request $request, $email)
    {
        // Find the active subscriber
        $subscriber = NewsletterSubscriber::where('email', $email)->firstOrFail();

        // Mark inactive
        $subscriber->update([
            'is_active' => false,
            'unsubscribed_at' => now()
        ]);

        return Inertia::render('Unsubscribed', [
            'email' => $email
        ]);
    }
}

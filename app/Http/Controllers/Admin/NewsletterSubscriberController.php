<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\NewsletterSubscribers\StoreNewsletterSubscriberRequest;
use App\Http\Requests\Admin\NewsletterSubscribers\UpdateNewsletterSubscriberRequest;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class NewsletterSubscriberController extends Controller
{
    /**
     * Display a listing of newsletter subscribers (Admin View).
     */
    public function index(): Response
    {
        $subscribers = NewsletterSubscriber::orderBy('created_at', 'desc')->get();

        return Inertia::render('Subscribers/Index', [
            'subscribers' => $subscribers,
        ]);
    }

    /**
     * Store a newly created subscriber in storage (Public View).
     */
    public function store(StoreNewsletterSubscriberRequest $request): RedirectResponse
    {
        $email = $request->input('email');

        // Check if subscriber exists already
        $subscriber = NewsletterSubscriber::where('email', $email)->first();

        if ($subscriber) {
            if (!$subscriber->is_active) {
                // Reactivate previously unsubscribed email
                $subscriber->update([
                    'is_active' => true,
                    'subscribed_at' => now(),
                    'unsubscribed_at' => null,
                ]);
            }
        } else {
            // Create a new active subscription
            NewsletterSubscriber::create([
                'email' => $email,
                'is_active' => true,
                'subscribed_at' => now(),
            ]);
        }

        return redirect()->back()->with('message', 'Thank you! You have successfully subscribed to our newsletter.');
    }

    /**
     * Update the subscriber active status (Admin View).
     */
    public function update(UpdateNewsletterSubscriberRequest $request, NewsletterSubscriber $newsletterSubscriber): RedirectResponse
    {
        $isActive = (bool) $request->input('is_active');

        $newsletterSubscriber->update([
            'is_active' => $isActive,
            'subscribed_at' => $isActive ? now() : $newsletterSubscriber->subscribed_at,
            'unsubscribed_at' => !$isActive ? now() : null,
        ]);

        return redirect()->back()->with('message', 'Subscriber status updated successfully.');
    }

    /**
     * Remove the subscriber completely from the database (Admin View).
     */
    public function destroy(NewsletterSubscriber $newsletterSubscriber): RedirectResponse
    {
        // Restricting subscription removals to administrators
        if (!auth()->user()->hasRole('admin')) {
            return redirect()->back()->with('error', 'Unauthorized action. Only administrators can delete subscribers.');
        }

        $newsletterSubscriber->delete();

        return redirect()->back()->with('message', 'Subscriber removed from database.');
    }
}

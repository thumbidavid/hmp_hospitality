<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ContactSubmissions\StoreContactSubmissionRequest;
use App\Http\Requests\Admin\ContactSubmissions\UpdateContactSubmissionRequest;
use App\Models\ContactSubmission;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ContactSubmissionController extends Controller
{
    /**
     * Display a listing of incoming messages (Admin View).
     */
    public function index(): Response
    {
        $submissions = ContactSubmission::orderBy('created_at', 'desc')->get();

        return Inertia::render('Contacts/Index', [
            'submissions' => $submissions,
        ]);
    }

    /**
     * Store a newly created inquiry in storage (Public View).
     */
    public function store(StoreContactSubmissionRequest $request): RedirectResponse
    {
        ContactSubmission::create($request->validated());

        return redirect()->back()->with('message', 'Thank you! Your message has been received.');
    }

    /**
     * Update the status of the submission (Admin View).
     */
    public function update(UpdateContactSubmissionRequest $request, ContactSubmission $contactSubmission): RedirectResponse
    {
        $contactSubmission->update($request->validated());

        return redirect()->back()->with('message', 'Message status updated successfully.');
    }

    /**
     * Remove the specified submission from storage (Admin View).
     */
    public function destroy(ContactSubmission $contactSubmission): RedirectResponse
    {
        // Restricting inbox deletions to administrators
        if (!auth()->user()->hasRole('admin')) {
            return redirect()->back()->with('error', 'Unauthorized action. Only administrators can delete contact inquiries.');
        }

        $contactSubmission->delete();

        return redirect()->back()->with('message', 'Message removed from database.');
    }
}

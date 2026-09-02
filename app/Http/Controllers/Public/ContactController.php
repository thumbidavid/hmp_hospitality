<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use App\Mail\NewContactSubmission; // Import your Mail class
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail; // Import Mail Facade
use Inertia\Inertia;

class ContactController extends Controller
{
    public function index()
    {
        return Inertia::render('Contact');
    }

    public function store(Request $request)
    {
        // 1. Validate submission parameters
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:150',
            'subject' => 'nullable|string|max:200',
            'message' => 'required|string'
        ]);

        // 2. Save directly into your contact_submissions table
        $submission = ContactSubmission::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'subject' => $validated['subject'] ?? 'General Website Enquiry',
            'message' => $validated['message'],
            'status' => 'new'
        ]);

        // 3. Send the Email Notification to hello@hmphospitality.co
        Mail::to('hello@hmphospitality.co')->send(new NewContactSubmission($submission));

        return back()->with('success', true);
    }
}

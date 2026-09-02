<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Rfps\StoreRfpSubmissionRequest;
use App\Http\Requests\Admin\Rfps\UpdateRfpSubmissionRequest;
use App\Models\Media;
use App\Models\RfpSubmission;
use App\Models\RfpNote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RfpController extends Controller
{
    /**
     * Display a listing of incoming RFPs (Admin View).
     */
    public function index(): Response
    {
        $rfps = RfpSubmission::orderBy('created_at', 'desc')->get();

        return Inertia::render('Rfps/Index', [
            'rfps' => $rfps,
        ]);
    }

    /**
     * Display details of a single RFP (Admin View).
     */
    public function show(RfpSubmission $rfp): Response
    {
        $rfp->load(['buyerCountry', 'buyerType', 'destination', 'properties.destination', 'agencyServices', 'notes.user']);

        return Inertia::render('Rfps/Show', [
            'rfp' => $rfp,
        ]);
    }

    /**
     * Store a newly created RFP from the public multi-step form (Public View).
     */
    public function store(StoreRfpSubmissionRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $attachmentUrl = null;

        // Generate dynamic serial reference number
        $latest = RfpSubmission::latest('id')->first();
        $nextId = $latest ? $latest->id + 1 : 1;
        $referenceNumber = 'RFP-' . date('Y') . '-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);

        // Resolve R2 attachment file URL
        if ($request->filled('attachment_id')) {
            $media = Media::find($request->input('attachment_id'));
            if ($media) {
                $attachmentUrl = $media->url;
            }
        }

        // Create the core RFP submission
        $rfp = RfpSubmission::create(array_merge($validated, [
            'reference_number' => $referenceNumber,
            'attachment_url' => $attachmentUrl,
        ]));

        // Sync shortlisted properties pivot
        $rfp->properties()->sync($validated['properties']);

        // Sync requested agency support services pivot
        if (!empty($validated['agency_services'])) {
            $rfp->agencyServices()->sync($validated['agency_services']);
        }

        return redirect()->back()->with('message', 'Thank you! Your RFP has been submitted successfully.');
    }

    /**
     * Update the pipeline status of the RFP (Admin View).
     */
    public function update(UpdateRfpSubmissionRequest $request, RfpSubmission $rfp): RedirectResponse
    {
        $rfp->update($request->validated());

        return redirect()->back()->with('message', 'RFP status updated successfully.');
    }

    /**
     * Add an internal staff pipeline note (Admin View).
     */
    public function addNote(Request $request, RfpSubmission $rfp): RedirectResponse
    {
        $request->validate([
            'note' => ['required', 'string', 'max:5000'],
        ]);

        RfpNote::create([
            'rfp_id' => $rfp->id,
            'user_id' => auth()->id(),
            'note' => $request->input('note'),
        ]);

        return redirect()->back()->with('message', 'Note added successfully.');
    }

    /**
     * Remove the specified RFP completely from the database (Admin View).
     */
    public function destroy(RfpSubmission $rfp): RedirectResponse
    {
        // Restricting RFP deletions to administrators
        if (!auth()->user()->hasRole('admin')) {
            return redirect()->back()->with('error', 'Unauthorized action. Only administrators can delete RFPs.');
        }

        $rfp->delete();

        return redirect()->route('app.admin.rfps.index')->with('message', 'RFP removed from pipeline.');
    }
}

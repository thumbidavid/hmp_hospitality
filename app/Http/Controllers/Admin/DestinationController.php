<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Destinations\StoreDestinationRequest;
use App\Http\Requests\Admin\Destinations\UpdateDestinationRequest;
use App\Models\Country;
use App\Models\Destination;
use App\Models\ContentHighlight;
use App\Models\Document;
use App\Models\Media;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class DestinationController extends Controller
{
    /**
     * Display a listing of the destinations.
     */
    public function index(): Response
    {
        $destinations = Destination::with('country')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return Inertia::render('Destinations/Index', [
            'destinations' => $destinations,
        ]);
    }

    /**
     * Show the form for creating a new destination.
     */
    public function create(): Response
    {
        $countries = Country::orderBy('name')->get();

        return Inertia::render('Destinations/Create', [
            'countries' => $countries,
        ]);
    }

    /**
     * Store a newly created destination in storage.
     */
    public function store(StoreDestinationRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $heroImageUrl = null;

        // Extract Hero Image URL from Media ID
        if ($request->filled('hero_image')) {
            $media = Media::find($request->input('hero_image'));
            if ($media) {
                $heroImageUrl = $media->url;
            }
        }

        $destination = Destination::create(array_merge($validated, [
            'hero_image_url' => $heroImageUrl,
        ]));

        // Process Highlights
        if (!empty($validated['reasons'])) {
            foreach ($validated['reasons'] as $reason) {
                ContentHighlight::create([
                    'subject_type' => 'destination',
                    'subject_id' => $destination->id,
                    'type' => 'reason_to_visit',
                    'text' => $reason['text'],
                    'sort_order' => $reason['sort_order'] ?? 0,
                ]);
            }
        }

        if (!empty($validated['attractions'])) {
            foreach ($validated['attractions'] as $attraction) {
                ContentHighlight::create([
                    'subject_type' => 'destination',
                    'subject_id' => $destination->id,
                    'type' => 'experience_attraction',
                    'text' => $attraction['text'],
                    'sort_order' => $attraction['sort_order'] ?? 0,
                ]);
            }
        }

        // Process Documents using their uploaded file IDs
        if (!empty($validated['docs'])) {
            foreach ($validated['docs'] as $doc) {
                if (!empty($doc['file_id'])) {
                    $docMedia = Media::find($doc['file_id']);
                    if ($docMedia) {
                        Document::create([
                            'subject_type' => 'destination',
                            'subject_id' => $destination->id,
                            'label' => $doc['label'],
                            'file_url' => $docMedia->url,
                            'sort_order' => $doc['sort_order'] ?? 0,
                        ]);
                    }
                }
            }
        }

        return redirect()->route('app.admin.destinations.index')->with('message', 'Destination created successfully.');
    }

    /**
     * Update the specified destination in storage.
     */
    public function update(UpdateDestinationRequest $request, Destination $destination): RedirectResponse
    {
        $validated = $request->validated();
        $heroImageUrl = $destination->hero_image_url;

        // Check for new hero image replacement ID
        if ($request->filled('hero_image')) {
            $media = Media::find($request->input('hero_image'));
            if ($media) {
                $heroImageUrl = $media->url;
            }
        }

        $destination->update(array_merge($validated, [
            'hero_image_url' => $heroImageUrl,
        ]));

        // Re-sync highlights
        $destination->highlights()->delete();

        if (!empty($validated['reasons'])) {
            foreach ($validated['reasons'] as $reason) {
                ContentHighlight::create([
                    'subject_type' => 'destination',
                    'subject_id' => $destination->id,
                    'type' => 'reason_to_visit',
                    'text' => $reason['text'],
                    'sort_order' => $reason['sort_order'] ?? 0,
                ]);
            }
        }

        if (!empty($validated['attractions'])) {
            foreach ($validated['attractions'] as $attraction) {
                ContentHighlight::create([
                    'subject_type' => 'destination',
                    'subject_id' => $destination->id,
                    'type' => 'experience_attraction',
                    'text' => $attraction['text'],
                    'sort_order' => $attraction['sort_order'] ?? 0,
                ]);
            }
        }

        // Process Documents Update
        $incomingDocIds = collect($validated['docs'] ?? [])->pluck('id')->filter()->toArray();
        $destination->documents()->whereNotIn('id', $incomingDocIds)->delete();

        if (!empty($validated['docs'])) {
            foreach ($validated['docs'] as $doc) {
                if (isset($doc['id'])) {
                    $existingDoc = Document::find($doc['id']);
                    $docUrl = $existingDoc->file_url;

                    if (!empty($doc['file_id'])) {
                        $docMedia = Media::find($doc['file_id']);
                        if ($docMedia) {
                            $docUrl = $docMedia->url;
                        }
                    }

                    $existingDoc->update([
                        'label' => $doc['label'],
                        'file_url' => $docUrl,
                        'sort_order' => $doc['sort_order'] ?? 0,
                    ]);
                } else {
                    if (!empty($doc['file_id'])) {
                        $docMedia = Media::find($doc['file_id']);
                        if ($docMedia) {
                            Document::create([
                                'subject_type' => 'destination',
                                'subject_id' => $destination->id,
                                'label' => $doc['label'],
                                'file_url' => $docMedia->url,
                                'sort_order' => $doc['sort_order'] ?? 0,
                            ]);
                        }
                    }
                }
            }
        }

        return redirect()->route('app.admin.destinations.index')->with('message', 'Destination updated successfully.');
    }

    /**
     * Show the form for editing the specified destination.
     */
    public function edit(Destination $destination): Response
    {
        $countries = Country::orderBy('name')->get();

        // Load nested child records
        $destination->load(['highlights', 'documents']);

        return Inertia::render('Destinations/Edit', [
            'destination' => $destination,
            'countries' => $countries,
        ]);
    }

    /**
     * Remove the specified destination from storage.
     */
    public function destroy(Destination $destination): RedirectResponse
    {
        // Restriction check to ensure only admins can soft-delete or remove members
        if (!auth()->user()->hasRole('admin')) {
            return redirect()->back()->with('error', 'Unauthorized action. Only administrators can delete destinations.');
        }

        // Clean up main hero image file in R2
        if ($destination->hero_image_url) {
            $heroPath = parse_url($destination->hero_image_url, PHP_URL_PATH);
            $heroPathClean = ltrim($heroPath, '/');
            if (Storage::disk('r2')->exists($heroPathClean)) {
                Storage::disk('r2')->delete($heroPathClean);
            }
        }

        // Clean up linked document files in R2
        foreach ($destination->documents as $doc) {
            $docPath = parse_url($doc->file_url, PHP_URL_PATH);
            $docPathClean = ltrim($docPath, '/');
            if (Storage::disk('r2')->exists($docPathClean)) {
                Storage::disk('r2')->delete($docPathClean);
            }
            $doc->delete();
        }

        // Cascade delete highlight text blocks
        $destination->highlights()->delete();

        $destination->delete();

        return redirect()->route('app.admin.destinations.index')->with('message', 'Destination deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Properties\StorePropertyRequest;
use App\Http\Requests\Admin\Properties\UpdatePropertyRequest;
use App\Models\Amenity;
use App\Models\Country;
use App\Models\Destination;
use App\Models\Document;
use App\Models\ContentHighlight;
use App\Models\Media;
use App\Models\PortfolioCategory;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PropertyController extends Controller
{
    /**
     * Display a listing of the properties.
     */
    public function index(): Response
    {
        $properties = Property::with(['portfolioCategory', 'destination', 'country'])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return Inertia::render('Properties/Index', [
            'properties' => $properties,
        ]);
    }

    /**
     * Show the form for creating a new property.
     */
    public function create(): Response
    {
        return Inertia::render('Properties/Create', [
            'categories' => PortfolioCategory::orderBy('sort_order')->get(),
            'destinations' => Destination::orderBy('sort_order')->orderBy('name')->get(),
            'countries' => Country::orderBy('name')->get(),
            'settings' => Setting::orderBy('name')->get(),
            'amenities' => Amenity::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created property in storage.
     */
    public function store(StorePropertyRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $featuredImageUrl = null;

        // Extract cover image from media table
        if ($request->filled('featured_image')) {
            $media = Media::find($request->input('featured_image'));
            if ($media) {
                $featuredImageUrl = $media->url;
            }
        }

        // Create core property record
        $property = Property::create(array_merge($validated, [
            'featured_image_url' => $featuredImageUrl,
        ]));

        // Sync pivot configurations (Settings & Amenities)
        if (!empty($validated['settings'])) {
            $property->settings()->sync($validated['settings']);
        }
        if (!empty($validated['amenities'])) {
            $property->amenities()->sync($validated['amenities']);
        }

        // Process Property Gallery Images
        if (!empty($validated['gallery'])) {
            foreach ($validated['gallery'] as $img) {
                if (!empty($img['media_id'])) {
                    $imageMedia = Media::find($img['media_id']);
                    if ($imageMedia) {
                        PropertyImage::create([
                            'property_id' => $property->id,
                            'image_url' => $imageMedia->url,
                            'caption' => $img['caption'] ?? null,
                            'is_cover' => !empty($img['is_cover']),
                            'sort_order' => $img['sort_order'] ?? 0,
                        ]);
                    }
                }
            }
        }

        // Process Content Highlights (Key Experiences)
        if (!empty($validated['key_experiences'])) {
            foreach ($validated['key_experiences'] as $experience) {
                ContentHighlight::create([
                    'subject_type' => 'property',
                    'subject_id' => $property->id,
                    'type' => 'key_experience',
                    'text' => $experience['text'],
                    'sort_order' => $experience['sort_order'] ?? 0,
                ]);
            }
        }

        // Process PDF Documents / Fact Sheets
        if (!empty($validated['docs'])) {
            foreach ($validated['docs'] as $doc) {
                if (!empty($doc['file_id'])) {
                    $docMedia = Media::find($doc['file_id']);
                    if ($docMedia) {
                        Document::create([
                            'subject_type' => 'property',
                            'subject_id' => $property->id,
                            'label' => $doc['label'],
                            'file_url' => $docMedia->url,
                            'sort_order' => $doc['sort_order'] ?? 0,
                        ]);
                    }
                }
            }
        }

        return redirect()->route('app.admin.properties.index')->with('message', 'Property created successfully.');
    }

    /**
     * Show the form for editing the specified property.
     */
    public function edit(Property $property): Response
    {
        $property->load(['settings', 'amenities', 'images', 'highlights', 'documents']);

        return Inertia::render('Properties/Edit', [
            'property' => $property,
            'categories' => PortfolioCategory::orderBy('sort_order')->get(),
            'destinations' => Destination::orderBy('sort_order')->orderBy('name')->get(),
            'countries' => Country::orderBy('name')->get(),
            'settings' => Setting::orderBy('name')->get(),
            'amenities' => Amenity::orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified property in storage.
     */
    public function update(UpdatePropertyRequest $request, Property $property): RedirectResponse
    {
        $validated = $request->validated();
        $featuredImageUrl = $property->featured_image_url;

        // Check for replaced cover media ID
        if ($request->filled('featured_image')) {
            $media = Media::find($request->input('featured_image'));
            if ($media) {
                $featuredImageUrl = $media->url;
            }
        }

        // Update core record details
        $property->update(array_merge($validated, [
            'featured_image_url' => $featuredImageUrl,
        ]));

        // Sync settings & amenities
        $property->settings()->sync($validated['settings'] ?? []);
        $property->amenities()->sync($validated['amenities'] ?? []);

        // Process Gallery Sync
        $incomingImageIds = collect($validated['gallery'] ?? [])->pluck('id')->filter()->toArray();
        $property->images()->whereNotIn('id', $incomingImageIds)->delete();

        if (!empty($validated['gallery'])) {
            foreach ($validated['gallery'] as $img) {
                if (isset($img['id'])) {
                    $existingImage = PropertyImage::find($img['id']);
                    $imageUrl = $existingImage->image_url;

                    if (!empty($img['media_id'])) {
                        $imageMedia = Media::find($img['media_id']);
                        if ($imageMedia) {
                            $imageUrl = $imageMedia->url;
                        }
                    }

                    $existingImage->update([
                        'image_url' => $imageUrl,
                        'caption' => $img['caption'] ?? null,
                        'is_cover' => !empty($img['is_cover']),
                        'sort_order' => $img['sort_order'] ?? 0,
                    ]);
                } else {
                    if (!empty($img['media_id'])) {
                        $imageMedia = Media::find($img['media_id']);
                        if ($imageMedia) {
                            PropertyImage::create([
                                'property_id' => $property->id,
                                'image_url' => $imageMedia->url,
                                'caption' => $img['caption'] ?? null,
                                'is_cover' => !empty($img['is_cover']),
                                'sort_order' => $img['sort_order'] ?? 0,
                            ]);
                        }
                    }
                }
            }
        }

        // Re-sync highlights
        $property->highlights()->delete();
        if (!empty($validated['key_experiences'])) {
            foreach ($validated['key_experiences'] as $experience) {
                ContentHighlight::create([
                    'subject_type' => 'property',
                    'subject_id' => $property->id,
                    'type' => 'key_experience',
                    'text' => $experience['text'],
                    'sort_order' => $experience['sort_order'] ?? 0,
                ]);
            }
        }

        // Re-sync documents
        $incomingDocIds = collect($validated['docs'] ?? [])->pluck('id')->filter()->toArray();
        $property->documents()->whereNotIn('id', $incomingDocIds)->delete();

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
                                'subject_type' => 'property',
                                'subject_id' => $property->id,
                                'label' => $doc['label'],
                                'file_url' => $docMedia->url,
                                'sort_order' => $doc['sort_order'] ?? 0,
                            ]);
                        }
                    }
                }
            }
        }

        return redirect()->route('app.admin.properties.index')->with('message', 'Property updated successfully.');
    }

    /**
     * Soft-delete the specified property.
     */
    public function destroy(Property $property): RedirectResponse
    {
        if (!auth()->user()->hasRole('admin')) {
            return redirect()->back()->with('error', 'Unauthorized action. Only administrators can delete properties.');
        }

        // Soft deletes the core record, keeping gallery and highlight records associated in case of restorations
        $property->delete();

        return redirect()->route('app.admin.properties.index')->with('message', 'Property deleted successfully.');
    }
}

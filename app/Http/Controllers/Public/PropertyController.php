<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Document;
use App\Models\ContentHighlight;
use Inertia\Inertia;
use Carbon\Carbon;

class PropertyController extends Controller
{
    public function show($slug)
    {
        // 1. Fetch active property by slug with relations
        $property = Property::where('slug', $slug)
            ->where('is_active', true)
            ->with(['portfolioCategory', 'country', 'settings', 'images'])
            ->firstOrFail();

        // 2. Fetch associated key experiences (content_highlights)
        $experiences = ContentHighlight::where('subject_type', 'property')
            ->where('subject_id', $property->id)
            ->where('type', 'key_experience')
            ->orderBy('sort_order')
            ->pluck('text')
            ->toArray();

        // Fallback default experiences if database is empty
        if (empty($experiences)) {
            $experiences = ["Curated beachside dining", "Private marine safari expeditions", "Traditional dhow sunset cruises"];
        }

        // 3. Format gallery images
        $gallery = $property->images->sortBy('sort_order')->pluck('image_url')->toArray();
        if (empty($gallery)) {
            $gallery = [$property->featured_image_url ?? 'https://images.unsplash.com/photo-1566073771259-1a873a6a8bed?auto=format&fit=crop&w=1600&q=80'];
        }

        // 4. Fetch 3 related properties (excluding itself, same country or setting)
        $settingIds = $property->settings->pluck('id')->toArray();
        $related = Property::where('id', '!=', $property->id)
            ->where('is_active', true)
            ->where(function ($q) use ($property, $settingIds) {
                $q->where('country_id', $property->country_id)
                    ->orWhereHas('settings', function ($sub) use ($settingIds) {
                        $sub->whereIn('settings.id', $settingIds);
                    });
            })
            ->with(['portfolioCategory', 'country'])
            ->take(3)
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->slug, // Ensure related links also point to slugs!
                    'name' => $p->name,
                    'category' => $p->portfolioCategory->name ?? '',
                    'city' => $p->city ?? '',
                    'country' => $p->country->name ?? '',
                    'image' => $p->featured_image_url ?? 'https://images.unsplash.com/photo-1566073771259-1a873a6a8bed?auto=format&fit=crop&w=800&q=80',
                    'blurb' => $p->tagline ?? $p->overview ?? '',
                    'rooms' => $p->number_of_rooms ?? 0,
                    'capacity' => $p->max_event_capacity ?? 0,
                    'type' => $p->portfolioCategory->name ?? ''
                ];
            });

        $documents = Document::where('subject_type', 'property')
            ->where('subject_id', $property->id)
            ->orderBy('sort_order', 'asc')
            ->get()
            ->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'label' => $doc->label,
                    'url' => $doc->file_url
                ];
            })
            ->toArray();

        // 5. Structure payload for the frontend
        $p = [
            'id' => $property->slug, // Use slug as the unique identifier for shortlist matching
            'name' => $property->name,
            'tagline' => $property->tagline,
            'blurb' => $property->tagline ?? $property->overview,
            'type' => $property->portfolioCategory->name ?? '',
            'collection' => $property->settings->first()->name ?? 'Luxury Escape',
            'city' => $property->city,
            'country' => $property->country->name ?? '',
            'region' => $property->country->region ?? '',
            'rooms' => $property->number_of_rooms ?? 0,
            'capacity' => $property->max_event_capacity ?? 0,
            'image' => $property->featured_image_url ?? 'https://images.unsplash.com/photo-1566073771259-1a873a6a8bed?auto=format&fit=crop&w=1600&q=80',
            'gallery' => $gallery,
            'website' => $property->website_url ?? '#',
            'overview' => $property->overview ?? '',
            'accommodation' => $property->accommodation_details ?? '',
            'events' => $property->meetings_facilities_details ?? '',
            'dining' => $property->dining_leisure_details ?? '',
            'sustainability' => $property->sustainability_details ?? '',
            'experiences' => $experiences,
            'latitude' => $property->latitude,
            'longitude' => $property->longitude,
            'documents' => $documents
        ];

        return Inertia::render('PropertyDetail', [
            'property' => $p,
            'related' => $related
        ]);
    }
}

<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use App\Models\Property;
use App\Models\Document;
use App\Models\ContentHighlight;
use Inertia\Inertia;
use Carbon\Carbon;

class DestinationController extends Controller
{
    public function show($slug)
    {
        // 1. Fetch active destination matching the slug (with country details)
        $destination = Destination::where('slug', $slug)
            ->where('is_active', true)
            ->with(['country'])
            ->firstOrFail();

        // 2. Fetch polymorphic reasons to meet (content_highlights)
        $reasons = ContentHighlight::where('subject_type', 'destination')
            ->where('subject_id', $destination->id)
            ->where('type', 'reason_to_visit')
            ->orderBy('sort_order')
            ->pluck('text')
            ->toArray();

        if (empty($reasons)) {
            $reasons = [
                "UNESCO World Heritage context and old-world aesthetics.",
                "Accessible flight routes with major airline connections.",
                "Bespoke settings tailored for executive corporate incentives."
            ];
        }

        // 3. Fetch polymorphic experiences and attractions (content_highlights)
        $experiences = ContentHighlight::where('subject_type', 'destination')
            ->where('subject_id', $destination->id)
            ->where('type', 'experience_attraction')
            ->orderBy('sort_order')
            ->pluck('text')
            ->toArray();

        if (empty($experiences)) {
            $experiences = ["UNESCO Stone Town", "Spice Plantation Tours", "Dhow Sunset Cruises", "Snorkeling Reefs"];
        }

        // 4. Fetch all active properties linked directly to this parent destination ID
        $venues = Property::where('destination_id', $destination->id)
            ->where('is_active', true)
            ->with(['portfolioCategory', 'country'])
            ->orderBy('sort_order')
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->slug, // Uses slug routing for property cards!
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

        $documents = Document::where('subject_type', 'destination')
            ->where('subject_id', $destination->id)
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

        // 5. Structure payload to match the original React state layout
        $d = [
            'id' => $destination->slug,
            'name' => $destination->name,
            'country' => $destination->country->name ?? '',
            'region' => $destination->country->region ?? '',
            'image' => $destination->hero_image_url ?? 'https://images.unsplash.com/photo-1549488344-1f9b8d2bd1f3?auto=format&fit=crop&w=1600&q=80',
            'intro' => $destination->intro_description ?? '',
            'access' => $destination->access_connectivity ?? 'International arrivals connecting via regional hubs.',
            'bestTime' => $destination->best_time_to_visit ?? 'Dry seasons from June to October.',
            'website' => $destination->website_url ?? '#',
            'reasons' => $reasons,
            'experiences' => $experiences,
            'documents' => $documents
        ];

        return Inertia::render('DestinationDetail', [
            'destination' => $d,
            'venues' => $venues
        ]);
    }
}

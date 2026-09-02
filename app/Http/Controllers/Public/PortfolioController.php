<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Destination;
use App\Models\Country;
use App\Models\PortfolioCategory;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class PortfolioController extends Controller
{
    public function index(Request $request)
    {
        // 1. Capture query parameters
        $q = $request->input('q');
        $cat = $request->input('cat', 'All');
        $region = $request->input('region');
        $country = $request->input('country');
        $city = $request->input('city');
        $type = $request->input('type');
        $settingId = $request->input('coll'); // Maps to dynamic Settings
        $minRooms = $request->input('minRooms');
        $minCap = $request->input('minCap');
        $featured = $request->boolean('featured');
        $sort = $request->input('sort', 'featured');

        // 2. Fetch lookup data dynamically from taxonomies to build dropdown selectors
        $countriesList = Country::pluck('name')->toArray();
        $regionsList = Country::whereNotNull('region')->distinct()->pluck('region')->toArray();
        $citiesList = Property::where('is_active', true)->whereNotNull('city')->distinct()->pluck('city')->toArray();
        $settingsList = Setting::get()->map(function ($s) {
            return ['id' => $s->id, 'name' => $s->name];
        })->toArray();

        // 3. Query properties
        $properties = Property::where('is_active', true)
            ->with(['portfolioCategory', 'country', 'settings'])
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->slug,
                    'name' => $p->name,
                    'category' => $p->portfolioCategory->name ?? '',
                    'city' => $p->city ?? '',
                    'country' => $p->country->name ?? '',
                    'region' => $p->country->region ?? '',
                    'image' => $p->featured_image_url ?? 'https://images.unsplash.com/photo-1566073771259-1a873a6a8bed?auto=format&fit=crop&w=800&q=80',
                    'blurb' => $p->tagline ?? $p->overview ?? '',
                    'rooms' => $p->number_of_rooms ?? 0,
                    'capacity' => $p->max_event_capacity ?? 0,
                    'meta' => ($p->number_of_rooms ?? 0) . ' rooms · ' . ($p->max_event_capacity ?? 0) . ' pax',
                    'website' => $p->website_url ?? '#',
                    'isFeatured' => (bool)$p->is_featured,
                    'type' => 'property',
                    'destination_id' => $p->destination_id,
                    'settings' => $p->settings->pluck('id')->toArray()
                ];
            });

        // 4. Query destinations
        $destinations = Destination::where('is_active', true)
            ->with(['country'])
            ->get()
            ->map(function ($d) {
                return [
                    'id' => $d->slug,
                    'db_id' => $d->id,
                    'name' => $d->name,
                    'category' => 'Destinations & DMOs',
                    'city' => '',
                    'country' => $d->country->name ?? '',
                    'region' => $d->country->region ?? '',
                    'image' => $d->hero_image_url ?? 'https://images.unsplash.com/photo-1549488344-1f9b8d2bd1f3?auto=format&fit=crop&w=800&q=80',
                    'blurb' => $d->intro_description ?? '',
                    'rooms' => 0,
                    'capacity' => 0,
                    'meta' => 'Destination & DMO',
                    'website' => $d->website_url ?? '#',
                    'isFeatured' => (bool)$d->is_featured,
                    'type' => 'destination',
                    'settings' => []
                ];
            });

        // 5. Combine sets (matching React's combined allItems array)
        $allItems = $properties->merge($destinations);

        // 6. Apply filters in-memory
        $filteredList = $allItems->filter(function ($it) use ($cat, $region, $country, $city, $q, $settingId, $minRooms, $minCap, $featured) {
            if ($cat !== 'All' && $it['category'] !== $cat) return false;
            if ($region && $it['region'] !== $region) return false;
            if ($country && $it['country'] !== $country) return false;
            if ($city && $it['city'] !== $city) return false;

            if ($q) {
                $searchStr = strtolower($it['name'] . ' ' . ($it['city'] ?? '') . ' ' . $it['country']);
                if (strpos($searchStr, strtolower($q)) === false) return false;
            }

            if ($it['type'] === 'property') {
                if ($settingId && !in_array($settingId, $it['settings'])) return false;
                if ($minRooms && $it['rooms'] < (int)$minRooms) return false;
                if ($minCap && $it['capacity'] < (int)$minCap) return false;
            }

            if ($featured && !$it['isFeatured']) return false;

            return true;
        });

        // 7. Apply Sorting
        if ($sort === 'name') {
            $filteredList = $filteredList->sortBy('name');
        } elseif ($sort === 'location') {
            $filteredList = $filteredList->sortBy(function ($it) {
                return $it['country'] . ($it['city'] ?? '');
            });
        } else {
            $filteredList = $filteredList->sortByDesc('isFeatured');
        }

        return Inertia::render('Portfolio', [
            'items' => array_values($filteredList->toArray()),
            'filters' => [
                'countries' => $countriesList,
                'regions' => $regionsList,
                'cities' => $citiesList,
                'settings' => $settingsList
            ],
            // Return back active filters to populate controls
            'requestFilters' => [
                'q' => $q ?? '',
                'cat' => $cat,
                'region' => $region ?? '',
                'country' => $country ?? '',
                'city' => $city ?? '',
                'coll' => $settingId ?? '',
                'minRooms' => $minRooms ?? '',
                'minCap' => $minCap ?? '',
                'featured' => $featured,
                'sort' => $sort
            ]
        ]);
    }
}

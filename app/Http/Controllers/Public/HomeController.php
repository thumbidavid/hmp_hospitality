<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Property;
use App\Models\Destination;
use App\Models\PortfolioCategory;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use Carbon\Carbon;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        // 1. Get unique country names
        $countries = Country::pluck('name')->toArray();

        // 2. Get active destination names for autocomplete tokens
        $destinationNames = Destination::where('is_active', true)
            ->pluck('name')
            ->toArray();

        // 3. Get unique active property cities
        $cities = Property::where('is_active', true)
            ->whereNotNull('city')
            ->distinct()
            ->pluck('city')
            ->toArray();

        // 4. Combine and unique the tokens (matching destTokens behavior)
        $destTokens = array_values(array_unique(array_merge($countries, $destinationNames, $cities)));

        // 5. Get detailed destination cards for the DestinationDiscovery component
        $destinations = Destination::where('is_active', true)
            ->with(['country'])
            ->withCount(['properties' => function ($query) {
                $query->where('is_active', true);
            }])
            ->orderBy('sort_order')
            ->get()
            ->map(function ($d) {
                return [
                    'id' => $d->slug,
                    'name' => $d->name,
                    'country' => $d->country->name ?? '',
                    'region' => $d->country->region ?? '',
                    'image' => $d->hero_image_url ?? 'https://images.unsplash.com/photo-1549488344-1f9b8d2bd1f3?auto=format&fit=crop&w=800&q=80',
                    'intro' => $d->intro_description ?? '',
                    'properties_count' => $d->properties_count
                ];
            });

        // 6. Gather properties matching your React selection rules
        $categories = PortfolioCategory::all();
        $featuredProperties = collect();

        foreach ($categories as $cat) {
            // Priority 1: Featured property in this category
            $prop = Property::where('is_active', true)
                ->where('portfolio_category_id', $cat->id)
                ->where('is_featured', true)
                ->with(['portfolioCategory', 'country'])
                ->first();

            // Priority 2 (Fallback): Any active property in this category
            if (!$prop) {
                $prop = Property::where('is_active', true)
                    ->where('portfolio_category_id', $cat->id)
                    ->with(['portfolioCategory', 'country'])
                    ->first();
            }

            if ($prop) {
                $featuredProperties->push($prop);
            }
        }

        // Fill up to 6 items using other active featured properties
        if ($featuredProperties->count() < 6) {
            $existingIds = $featuredProperties->pluck('id')->toArray();
            $extraFeatured = Property::where('is_active', true)
                ->where('is_featured', true)
                ->whereNotIn('id', $existingIds)
                ->with(['portfolioCategory', 'country'])
                ->take(6 - $featuredProperties->count())
                ->get();

            $featuredProperties = $featuredProperties->merge($extraFeatured);
        }

        // Map collection attributes to clean frontend object variables
        $featured = $featuredProperties->take(6)->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'portfolioCategory' => $p->portfolioCategory->name ?? '',
                'city' => $p->city ?? '',
                'country' => $p->country->name ?? '',
                'image' => $p->featured_image_url ?? 'https://images.unsplash.com/photo-1566073771259-1a873a6a8bed?auto=format&fit=crop&w=800&q=80',
                'blurb' => $p->tagline ?? $p->overview ?? '',
            ];
        });

        // 7. Get dynamic blog categories for taxonomy chips
        $blogCategories = BlogCategory::pluck('name')->toArray();

        // 8. Get active, published blog posts (eager load relation)
        $blogPosts = BlogPost::where('is_active', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', Carbon::now())
            ->with('blogCategory') // Maps the relation
            ->orderBy('published_at', 'desc')
            ->take(4) // 1 main lead + 3 list items
            ->get()
            ->map(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'slug' => $post->slug,
                    'excerpt' => $post->excerpt,
                    'image' => $post->featured_image_url ?? 'https://images.unsplash.com/photo-1501504905252-473c47e087f8?auto=format&fit=crop&w=800&q=80',
                    'category' => $post->blogCategory->name ?? 'General',
                    'read' => $post->read_minutes ? "{$post->read_minutes} min" : '3 min',
                    'date' => Carbon::parse($post->published_at)->format('M d, Y')
                ];
            });

        return Inertia::render('Home', [
            'destTokens' => $destTokens,
            'destinations' => $destinations,
            'featured' => $featured,
            'blogCategories' => $blogCategories,
            'blogPosts' => $blogPosts
        ]);
    }
}

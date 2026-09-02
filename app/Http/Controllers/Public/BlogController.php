<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use Carbon\Carbon;
use Inertia\Inertia;

class BlogController extends Controller
{
    /**
     * Display a listing of all active blog posts (Stories Index).
     */
    public function index()
    {
        // 1. Fetch active blog categories dynamically to populate the tab chips
        $categories = BlogCategory::pluck('name')->toArray();

        // 2. Query active, published blog posts (eager load relation)
        $posts = BlogPost::where('is_active', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', Carbon::now())
            ->with('blogCategory') // Maps the relation
            ->orderBy('published_at', 'desc')
            ->get()
            ->map(function ($post) {
                return [
                    'id' => $post->slug, // Uses slug as ID for clean URL routing
                    'slug' => $post->slug,
                    'title' => $post->title,
                    'excerpt' => $post->excerpt,
                    'image' => $post->featured_image_url ?? 'https://images.unsplash.com/photo-1501504905252-473c47e087f8?auto=format&fit=crop&w=800&q=80',
                    'category' => $post->blogCategory->name ?? 'General',
                    'read' => $post->read_minutes ? "{$post->read_minutes} min" : '3 min',
                    'date' => Carbon::parse($post->published_at)->format('M d, Y')
                ];
            });

        return Inertia::render('Articles', [
            'categories' => $categories,
            'posts' => $posts
        ]);
    }

    /**
     * Display details of a single blog post by its slug.
     */
    public function show($slug)
    {
        // 1. Fetch active post matching the slug (with category and author details)
        $post = BlogPost::where('slug', $slug)
            ->where('is_active', true)
            ->with(['blogCategory', 'author'])
            ->firstOrFail();

        // 2. Fetch 2 active related blog posts (excluding itself)
        $related = BlogPost::where('id', '!=', $post->id)
            ->where('is_active', true)
            ->with('blogCategory')
            ->take(2)
            ->get()
            ->map(function ($a) {
                return [
                    'id' => $a->slug, // Uses slug routing for related cards
                    'title' => $a->title,
                    'category' => $a->blogCategory->name ?? 'General',
                    'image' => $a->featured_image_url ?? 'https://images.unsplash.com/photo-1549488344-1f9b8d2bd1f3?auto=format&fit=crop&w=800&q=80',
                    'read' => $a->read_minutes ? "{$a->read_minutes} min" : '3 min',
                    'date' => Carbon::parse($a->published_at)->format('M d, Y')
                ];
            });

        // 3. Structure payload for the frontend
        $article = [
            'id' => $post->slug,
            'title' => $post->title,
            'excerpt' => $post->excerpt,
            'content' => $post->content, // Dynamic LONGTEXT HTML or Markdown content
            'image' => $post->featured_image_url ?? 'https://images.unsplash.com/photo-1549488344-1f9b8d2bd1f3?auto=format&fit=crop&w=1600&q=80',
            'category' => $post->blogCategory->name ?? 'General',
            'author' => $post->author->name ?? 'HMP Editorial',
            'read' => $post->read_minutes ? "{$post->read_minutes} min" : '3 min',
            'date' => Carbon::parse($post->published_at)->format('M d, Y')
        ];

        return Inertia::render('ArticleDetail', [
            'article' => $article,
            'related' => $related
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BlogPosts\StoreBlogPostRequest;
use App\Http\Requests\Admin\BlogPosts\UpdateBlogPostRequest;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Media;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class BlogPostController extends Controller
{
    /**
     * Display a listing of the blog posts.
     */
    public function index(): Response
    {
        $posts = BlogPost::with(['blogCategory', 'author'])
            ->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('BlogPosts/Index', [
            'posts' => $posts,
        ]);
    }

    /**
     * Show the form for creating a new blog post.
     */
    public function create(): Response
    {
        $categories = BlogCategory::orderBy('name')->get();

        return Inertia::render('BlogPosts/Create', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created blog post in storage.
     */
    public function store(StoreBlogPostRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $featuredImageUrl = null;

        // Resolve cover media URL
        if ($request->filled('featured_image')) {
            $media = Media::find($request->input('featured_image'));
            if ($media) {
                $featuredImageUrl = $media->url;
            }
        }

        BlogPost::create(array_merge($validated, [
            'author_id' => auth()->id(), // Assigns the logged-in editor/admin as author
            'featured_image_url' => $featuredImageUrl,
        ]));

        return redirect()->route('app.admin.blog-posts.index')->with('message', 'Article created successfully.');
    }

    /**
     * Show the form for editing the specified blog post.
     */
    public function edit(BlogPost $blogPost): Response
    {
        $categories = BlogCategory::orderBy('name')->get();

        return Inertia::render('BlogPosts/Edit', [
            'post' => $blogPost,
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified blog post in storage.
     */
    public function update(UpdateBlogPostRequest $request, BlogPost $blogPost): RedirectResponse
    {
        $validated = $request->validated();
        $featuredImageUrl = $blogPost->featured_image_url;

        // Re-resolve media cover URL if replaced
        if ($request->filled('featured_image')) {
            $media = Media::find($request->input('featured_image'));
            if ($media) {
                $featuredImageUrl = $media->url;
            }
        }

        $blogPost->update(array_merge($validated, [
            'featured_image_url' => $featuredImageUrl,
        ]));

        return redirect()->route('app.admin.blog-posts.index')->with('message', 'Article updated successfully.');
    }

    /**
     * Soft-delete the specified blog post.
     */
    public function destroy(BlogPost $blogPost): RedirectResponse
    {
        if (!auth()->user()->hasRole('admin')) {
            return redirect()->back()->with('error', 'Unauthorized action. Only administrators can delete blog articles.');
        }

        $blogPost->delete();

        return redirect()->route('app.admin.blog-posts.index')->with('message', 'Article deleted successfully.');
    }
}

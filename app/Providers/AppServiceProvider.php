<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

// Models
use App\Models\BlogPost;
use App\Models\Property;
use App\Models\Destination;

// Observers
use App\Observers\BlogPostObserver;
use App\Observers\PropertyObserver;
use App\Observers\DestinationObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        Vite::prefetch(concurrency: 3);

        // Register Model Observers to automate newsletter dispatch on creation
        BlogPost::observe(BlogPostObserver::class);
        Property::observe(PropertyObserver::class);
        Destination::observe(DestinationObserver::class);
    }
}

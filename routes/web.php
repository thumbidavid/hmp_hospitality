<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\PortfolioCategoryController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\AmenityController;
use App\Http\Controllers\Admin\BuyerTypeController;
use App\Http\Controllers\Admin\AgencySupportServiceController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\DestinationController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\PropertyController;
use App\Http\Controllers\Admin\BlogPostController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\ContactSubmissionController;
use App\Http\Controllers\Admin\NewsletterSubscriberController;
use App\Http\Controllers\Admin\RfpController;
use App\Http\Controllers\Admin\UserController;


// Public Controllers
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\PortfolioController;
use App\Http\Controllers\Public\PropertyController as PublicPropertyController;
use App\Http\Controllers\Public\DestinationController as PublicDestinationController;
use App\Http\Controllers\Public\AgencyController;
use App\Http\Controllers\Public\AboutController;
use App\Http\Controllers\Public\ServicesController;
use App\Http\Controllers\Public\PartnerController as PublicPartnerController;
use App\Http\Controllers\Public\RfpController as PublicRfpController;
use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Public\PolicyController;
use App\Http\Controllers\Public\BlogController;
use App\Http\Controllers\Public\SubscriptionController;

use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| 1. MARKETING ROUTES (PUBLIC - NO AUTH)
|--------------------------------------------------------------------------
*/

Route::group([], function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::get('/portfolio', [PortfolioController::class, 'index'])->name('portfolio');

    Route::get('/portfolio/{slug}', [PublicPropertyController::class, 'show'])->name('portfolio.show');

    Route::get('/destinations/{slug}', [PublicDestinationController::class, 'show'])->name('destination.show');

    Route::get('/stories', [BlogController::class, 'index'])->name('stories.index');
    Route::get('/stories/{slug}', [BlogController::class, 'show'])->name('stories.show');

    Route::get('/hmp-agency', [AgencyController::class, 'index'])->name('agency');
    Route::post('/hmp-agency', [AgencyController::class, 'store'])->name('agency.store');

    Route::get('/about', [AboutController::class, 'index'])->name('about');

    Route::get('/services', [ServicesController::class, 'index'])->name('services');

    Route::get('/partners', [PublicPartnerController::class, 'index'])->name('partners');
    Route::post('/partners', [PublicPartnerController::class, 'store'])->name('partners.store');

    Route::get('/rfp', [PublicRfpController::class, 'index'])->name('rfp');
    Route::post('/rfp', [PublicRfpController::class, 'store'])->name('rfp.store');

    Route::get('/contact', [ContactController::class, 'index'])->name('contact');
    Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

    Route::post('/newsletter/subscribe', [NewsletterSubscriberController::class, 'store'])->name('newsletter.subscribe');
    Route::get('/unsubscribe/{email}', [SubscriptionController::class, 'unsubscribe'])
        ->name('newsletter.unsubscribe')
        ->middleware('signed');

    // Legal & Policy Routes
    Route::get('/privacy-policy', [PolicyController::class, 'privacy'])->name('privacy');
    Route::get('/cookie-policy', [PolicyController::class, 'cookiePolicy'])->name('cookie.policy');
    Route::get('/terms-of-use', [PolicyController::class, 'terms'])->name('terms');
});

Route::fallback(function () {
    return Inertia::render('PageNotFound');
});


// Route::post('/rfp', [RfpController::class, 'store'])->name('rfp.store');

/*
|--------------------------------------------------------------------------
| 2. AUTH ROUTES (BREEZE / LOGIN / REGISTER)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| 3. STAFF / ADMIN PANEL ROUTES (AUTHENTICATED)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])
    ->prefix('app')
    ->name('app.')
    ->group(function () {

        // Core Dashboard View
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Placeholder routes to prevent frontend Ziggy/Inertia routing errors.
        // All paths point back to a standard view until controller files are implemented.
        Route::prefix('admin')->name('admin.')->group(function () {

            // Asynchronous Media Upload Handler (Used by FileUploader.vue)
            Route::post('/media', [MediaController::class, 'store'])->name('media.store');

            // RFP Pipeline
            Route::resource('rfps', RfpController::class)
                ->except(['create', 'edit', 'store'])
                ->parameters(['rfps' => 'rfp']);

            Route::post('/rfps/{rfp}/notes', [RfpController::class, 'addNote'])->name('rfps.notes.store');

            Route::resource('contacts', ContactSubmissionController::class)
                ->except(['create', 'edit', 'store'])
                ->parameters(['contacts' => 'contact_submission']);
            Route::resource('subscribers', NewsletterSubscriberController::class)
                ->except(['create', 'edit', 'store'])
                ->parameters(['subscribers' => 'newsletter_subscriber']);

            // Portfolio Content
            Route::resource('properties', PropertyController::class);
            Route::resource('destinations', DestinationController::class);

            // Editorial
            Route::resource('blog-posts', BlogPostController::class);
            Route::resource('partners', PartnerController::class)->except(['create', 'edit']);

            // Taxonomies Dropdown
            Route::resource('countries', CountryController::class)->except(['create', 'edit']);
            Route::resource('portfolio-categories', PortfolioCategoryController::class)->except(['create', 'edit']);
            Route::resource('settings', SettingController::class)->except(['create', 'edit']);
            Route::resource('amenities', AmenityController::class)->except(['create', 'edit']);
            Route::resource('buyer-types', BuyerTypeController::class)->except(['create', 'edit']);
            Route::resource('agency-services', AgencySupportServiceController::class)
                ->except(['create', 'edit'])
                ->parameters(['agency-services' => 'agency_support_service']);
            Route::resource('blog-categories', BlogCategoryController::class)->except(['create', 'edit']);

            // Only administrators can manage User accounts
            Route::middleware(['role:admin'])->group(function () {
                Route::resource('users', UserController::class)->except(['create', 'edit']);
            });
        });
    });

/*
|--------------------------------------------------------------------------
| 4. TEST UTILITIES
|--------------------------------------------------------------------------
*/
Route::get('/test-log', function () {
    $user = auth()->user();
    $user->notify(
        new \App\Notifications\GeneralNotification([
            'title' => 'Test Notification',
            'message' => 'This is a manual test',
            'url' => '#',
            'type' => 'test',
            'priority' => 'high',
        ]),
    );
    return "Notification sent. Check DB table 'notifications'.";
});

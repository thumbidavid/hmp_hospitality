<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Illuminate\Support\Facades\Log;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    // app/Http/Middleware/HandleInertiaRequests.php

    public function rootView(Request $request): string
    {

        // App bundle: all authenticated application areas
        if (
            $request->is('app*') ||
            $request->routeIs(
                'login',
                'register',
                'password.*',
                'verification.*',
                'invitations.*',
                'rsvp.*',
            )
        ) {
            return 'app';
        }

        // Default: Marketing/Public bundle
        return 'marketing';
    }

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user ? [
                    'id'          => $user->id,
                    'name'        => $user->name,
                    'email'       => $user->email,
                    'avatar_url'  => $user->avatar_url,
                    // Spatie permission integration
                    'roles'         => $user->getRoleNames(),
                    'permissions'   => $user->getAllPermissions()->pluck('name'),
                ] : null,
            ],
            // Flash messages for UI feedback (e.g., success/error toasts)
            'flash' => [
                'message' => fn() => $request->session()->get('message'),
                'error'   => fn() => $request->session()->get('error'),
            ],
            'ziggy' => fn() => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
        ]);
    }
}

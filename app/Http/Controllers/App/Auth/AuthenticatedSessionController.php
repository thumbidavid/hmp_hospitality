<?php

namespace App\Http\Controllers\App\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // 1. Authenticate user via LoginRequest
        $request->authenticate();

        // 2. Regenerate session to prevent fixation
        $request->session()->regenerate();

        $user = $request->user();

        /**
         * 3. Redirect based on Role
         * We use the 'intended' method so that if they were trying to access
         * a specific page before login, they get sent there instead.
         */

        if ($user->hasRole('admin')) {
            return redirect()->intended(route('app.dashboard'));
        }

        if ($user->hasRole('organiser')) {
            return redirect()->intended(route('app.dashboard'));
        }

        // Default redirect for 'reader' or other roles
        return redirect()->intended(route('app.dashboard'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}

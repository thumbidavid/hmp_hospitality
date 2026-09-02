<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class PolicyController extends Controller
{
    /**
     * Render the public Privacy Policy page.
     */
    public function privacy()
    {
        return Inertia::render('Privacy');
    }

    /**
     * Render the public Cookie Policy page.
     */
    public function cookiePolicy()
    {
        return Inertia::render('CookiePolicy');
    }

    /**
     * Render the public Terms of Use page.
     */
    public function terms()
    {
        return Inertia::render('Terms');
    }
}

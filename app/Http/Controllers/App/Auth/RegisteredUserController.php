<?php

namespace App\Http\Controllers\App\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role; // <--- ADD THIS (Assuming your Role model is here)
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // <--- ADD THIS
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|string|email|max:255|unique:users',
            'password'   => ['required', 'confirmed', Rules\Password::defaults()],
            'company'    => 'required|string|max:150',
            'job_title'  => 'nullable|string|max:150', // Added as it was missing from validation
        ]);

        // 1. Get the 'organiser' role
        $role = Role::findOrCreate('organiser');

        // 2. Create the user
        $user = User::create([
            'role_id'    => $role->id,
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'company'    => $request->company,
            'job_title'  => $request->job_title,
        ]);

        // 3. Assign the Spatie role
        $user->assignRole('organiser');

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Media;
use App\Http\Requests\Admin\Users\StoreUserRequest;  // Placed in your singular User folder
use App\Http\Requests\Admin\Users\UpdateUserRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the users (Staff Directory).
     */
    public function index(): Response
    {
        return Inertia::render('Users/Index', [
            'users' => User::with('roles')->get(),
            'roles' => Role::all(),
        ]);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $avatarUrl = null;

        // Resolve asynchronous profile avatar ID to a public CDN URL
        if ($request->filled('avatar')) {
            $media = Media::find($request->input('avatar'));
            if ($media) {
                $avatarUrl = $media->url;
            }
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_active' => $request->boolean('is_active', true),
            'avatar_url' => $avatarUrl,
        ]);

        // Normalize: Ensure roles is always an array
        $roles = $request->input('roles');
        $rolesArray = is_array($roles) ? $roles : [$roles];

        $user->syncRoles($rolesArray);

        return redirect()->back()->with('message', 'User created successfully');
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'is_active' => $request->boolean('is_active'),
        ];

        // Only update password if a new one is provided
        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        // Re-resolve profile image URL if a new image was uploaded
        if ($request->filled('avatar')) {
            $media = Media::find($request->input('avatar'));
            if ($media) {
                $userData['avatar_url'] = $media->url;
            }
        }

        $user->update($userData);

        // Normalize: Ensure roles is always an array
        $roles = $request->input('roles');
        $rolesArray = is_array($roles) ? $roles : [$roles];

        $user->syncRoles($rolesArray);

        return redirect()->back()->with('message', 'User updated successfully');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        // 🚨 UX Safety Check: Prevent administrator self-deletion
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Action denied. You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->back()->with('message', 'User deleted successfully');
    }
}

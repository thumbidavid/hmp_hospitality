<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Settings\StoreSettingRequest;
use App\Http\Requests\Admin\Settings\UpdateSettingRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $settings = Setting::orderBy('name')->get();

        return Inertia::render('Settings/Index', [
            'settings' => $settings,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSettingRequest $request): RedirectResponse
    {
        Setting::create($request->validated());

        return redirect()->back()->with('message', 'Setting created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSettingRequest $request, Setting $setting): RedirectResponse
    {
        $setting->update($request->validated());

        return redirect()->back()->with('message', 'Setting updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Setting $setting): RedirectResponse
    {
        // Restricting taxonomic deletions to administrators
        if (!auth()->user()->hasRole('admin')) {
            return redirect()->back()->with('error', 'Unauthorized action. Only administrators can delete settings.');
        }

        $setting->delete();

        return redirect()->back()->with('message', 'Setting deleted successfully.');
    }
}

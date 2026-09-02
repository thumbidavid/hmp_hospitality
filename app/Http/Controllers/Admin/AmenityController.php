<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Amenities\StoreAmenityRequest;
use App\Http\Requests\Admin\Amenities\UpdateAmenityRequest;
use App\Models\Amenity;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AmenityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $amenities = Amenity::orderBy('name')->get();

        return Inertia::render('Amenities/Index', [
            'amenities' => $amenities,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAmenityRequest $request): RedirectResponse
    {
        Amenity::create($request->validated());

        return redirect()->back()->with('message', 'Amenity created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAmenityRequest $request, Amenity $amenity): RedirectResponse
    {
        $amenity->update($request->validated());

        return redirect()->back()->with('message', 'Amenity updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Amenity $amenity): RedirectResponse
    {
        // Restricting taxonomic deletions to administrators
        if (!auth()->user()->hasRole('admin')) {
            return redirect()->back()->with('error', 'Unauthorized action. Only administrators can delete amenities.');
        }

        $amenity->delete();

        return redirect()->back()->with('message', 'Amenity deleted successfully.');
    }
}

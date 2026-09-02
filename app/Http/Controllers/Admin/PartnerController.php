<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Partners\StorePartnerRequest;
use App\Http\Requests\Admin\Partners\UpdatePartnerRequest;
use App\Models\Media;
use App\Models\Partner;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PartnerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $partners = Partner::orderBy('sort_order')->orderBy('name')->get();

        return Inertia::render('Partners/Index', [
            'partners' => $partners,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePartnerRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $logoUrl = null;

        // Resolve logo URL from pre-uploaded Media ID
        if ($request->filled('logo')) {
            $media = Media::find($request->input('logo'));
            if ($media) {
                $logoUrl = $media->url;
            }
        }

        Partner::create(array_merge($validated, [
            'logo_url' => $logoUrl,
        ]));

        return redirect()->back()->with('message', 'Partner registered successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePartnerRequest $request, Partner $partner): RedirectResponse
    {
        $validated = $request->validated();
        $logoUrl = $partner->logo_url;

        // Re-resolve logo URL if a new image was uploaded
        if ($request->filled('logo')) {
            $media = Media::find($request->input('logo'));
            if ($media) {
                $logoUrl = $media->url;
            }
        }

        $partner->update(array_merge($validated, [
            'logo_url' => $logoUrl,
        ]));

        return redirect()->back()->with('message', 'Partner details updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Partner $partner): RedirectResponse
    {
        // Restricting taxonomic/partner directory deletions to administrators
        if (!auth()->user()->hasRole('admin')) {
            return redirect()->back()->with('error', 'Unauthorized action. Only administrators can delete partners.');
        }

        $partner->delete();

        return redirect()->back()->with('message', 'Partner removed successfully.');
    }
}

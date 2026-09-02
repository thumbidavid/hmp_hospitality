<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AgencySupportServices\StoreAgencySupportServiceRequest;
use App\Http\Requests\Admin\AgencySupportServices\UpdateAgencySupportServiceRequest;
use App\Models\AgencySupportService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AgencySupportServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $services = AgencySupportService::orderBy('sort_order')->orderBy('name')->get();

        return Inertia::render('AgencySupportServices/Index', [
            'services' => $services,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAgencySupportServiceRequest $request): RedirectResponse
    {
        AgencySupportService::create($request->validated());

        return redirect()->back()->with('message', 'Support Service created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAgencySupportServiceRequest $request, AgencySupportService $agencySupportService): RedirectResponse
    {
        $agencySupportService->update($request->validated());

        return redirect()->back()->with('message', 'Support Service updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AgencySupportService $agencySupportService): RedirectResponse
    {
        // Restricting taxonomic deletions to administrators
        if (!auth()->user()->hasRole('admin')) {
            return redirect()->back()->with('error', 'Unauthorized action. Only administrators can delete support services.');
        }

        $agencySupportService->delete();

        return redirect()->back()->with('message', 'Support Service deleted successfully.');
    }
}

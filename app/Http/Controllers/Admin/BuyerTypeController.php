<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BuyerTypes\StoreBuyerTypeRequest;
use App\Http\Requests\Admin\BuyerTypes\UpdateBuyerTypeRequest;
use App\Models\BuyerType;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class BuyerTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $buyerTypes = BuyerType::orderBy('name')->get();

        return Inertia::render('BuyerTypes/Index', [
            'buyerTypes' => $buyerTypes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBuyerTypeRequest $request): RedirectResponse
    {
        BuyerType::create($request->validated());

        return redirect()->back()->with('message', 'Buyer Type created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBuyerTypeRequest $request, BuyerType $buyerType): RedirectResponse
    {
        $buyerType->update($request->validated());

        return redirect()->back()->with('message', 'Buyer Type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BuyerType $buyerType): RedirectResponse
    {
        // Restricting taxonomic deletions to administrators
        if (!auth()->user()->hasRole('admin')) {
            return redirect()->back()->with('error', 'Unauthorized action. Only administrators can delete buyer types.');
        }

        $buyerType->delete();

        return redirect()->back()->with('message', 'Buyer Type deleted successfully.');
    }
}

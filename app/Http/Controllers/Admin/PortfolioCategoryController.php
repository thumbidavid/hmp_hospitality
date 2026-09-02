<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PortfolioCategories\StorePortfolioCategoryRequest;
use App\Http\Requests\Admin\PortfolioCategories\UpdatePortfolioCategoryRequest;
use App\Models\PortfolioCategory;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PortfolioCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $categories = PortfolioCategory::orderBy('sort_order')->orderBy('name')->get();

        return Inertia::render('PortfolioCategories/Index', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePortfolioCategoryRequest $request): RedirectResponse
    {
        PortfolioCategory::create($request->validated());

        return redirect()->back()->with('message', 'Category created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePortfolioCategoryRequest $request, PortfolioCategory $portfolioCategory): RedirectResponse
    {
        $portfolioCategory->update($request->validated());

        return redirect()->back()->with('message', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PortfolioCategory $portfolioCategory): RedirectResponse
    {
        // Restricting taxonomic deletions to administrators
        if (!auth()->user()->hasRole('admin')) {
            return redirect()->back()->with('error', 'Unauthorized action. Only administrators can delete portfolio categories.');
        }

        $portfolioCategory->delete();

        return redirect()->back()->with('message', 'Category deleted successfully.');
    }
}

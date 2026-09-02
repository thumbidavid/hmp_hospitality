<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use App\Models\Country;
use App\Models\PortfolioCategory;
use App\Models\Partner; // Import the Partner Model
use Illuminate\Http\Request;
use Inertia\Inertia;

class PartnerController extends Controller
{
    public function index()
    {
        $countriesList = Country::pluck('name')->toArray();
        $categoriesList = PortfolioCategory::pluck('name')->toArray();

        // Query active partners grouped dynamically by their ENUM category types
        $partnerGroups = Partner::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->get()
            ->groupBy('partner_type')
            ->map(function ($group, $type) {
                // Map ENUM values to public visual headers
                $typeLabels = [
                    'gds' => 'Global GDS Partners',
                    'dmc' => 'Destination Management Companies',
                    'tourism_board' => 'Tourism Boards & DMOs',
                    'association' => 'Industry Associations',
                    'technology' => 'Technology Partners',
                    'distribution' => 'Distribution Channels',
                    'media' => 'Media Partners',
                    'other' => 'Strategic Partners',
                ];

                // Map ENUM values to public descriptions
                $typeBlurbs = [
                    'gds' => 'Strategic technological systems ensuring seamless GDS connectivity.',
                    'dmc' => 'Certified destination management teams coordinating ground operations.',
                    'tourism_board' => 'Institutional tourism offices positioning priority markets.',
                    'association' => 'Active professional bodies establishing global hospitality standards.',
                    'technology' => 'Innovative systems powering hospitality commerce.',
                    'distribution' => 'Live travel channels connecting global booking inventories.',
                    'media' => 'Connected editorial networks publishing industry news.',
                    'other' => 'Allied global organizations supporting growth.',
                ];

                return [
                    'category' => $typeLabels[$type] ?? ucfirst($type),
                    'blurb' => $typeBlurbs[$type] ?? '',
                    'partners' => $group->map(function ($p) {
                        return [
                            'id' => $p->id,
                            'name' => $p->name,
                            'logo' => $p->logo_url, // Maps to R2 public CDN URLs
                            'website' => $p->website_url ?? '#',
                            'description' => $p->description
                        ];
                    })->toArray()
                ];
            })
            ->values() // Reset associative indices
            ->toArray();

        return Inertia::render('Partners', [
            'countriesList' => $countriesList,
            'categoriesList' => $categoriesList,
            'partnerGroups' => $partnerGroups // Pass real partners array to page props
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'orgName' => 'required|string|max:200',
            'category' => 'required|string|max:100',
            'contact' => 'required|string|max:150',
            'email' => 'required|email|max:150',
            'jobTitle' => 'nullable|string|max:150',
            'phone' => 'nullable|string|max:30',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:150',
            'website' => 'nullable|url|max:255',
            'units' => 'nullable|integer',
            'capacity' => 'nullable|integer',
            'currentMarkets' => 'nullable|string',
            'requiredMarkets' => 'nullable|string',
            'services' => 'nullable|string',
            'additional' => 'nullable|string',
        ]);

        $message = "Represented Member Onboarding Questionnaire:\n\n"
            . "• Portfolio Category: " . ($validated['category'] ?? 'N/A') . "\n"
            . "• Job Title: " . ($validated['jobTitle'] ?? 'N/A') . "\n"
            . "• Location: " . ($validated['city'] ?? 'N/A') . ", " . ($validated['country'] ?? 'N/A') . "\n"
            . "• Website: " . ($validated['website'] ?? 'N/A') . "\n"
            . "• Capacity Specs: " . ($validated['units'] ?? '0') . " rooms | " . ($validated['capacity'] ?? '0') . " pax largest\n"
            . "• Current Source Markets: " . ($validated['currentMarkets'] ?? 'N/A') . "\n"
            . "• Required Markets: " . ($validated['requiredMarkets'] ?? 'N/A') . "\n"
            . "• Services Required: " . ($validated['services'] ?? 'N/A') . "\n"
            . "• Additional Information: " . ($validated['additional'] ?? 'N/A');

        ContactSubmission::create([
            'name' => $validated['contact'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'company' => $validated['orgName'],
            'subject' => 'Represented Member Onboarding Enquiry - ' . $validated['orgName'],
            'message' => $message,
            'status' => 'new'
        ]);

        return back()->with('success', true);
    }
}

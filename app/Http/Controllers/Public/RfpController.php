<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Rfps\StoreRfpSubmissionRequest;
use App\Models\RfpSubmission;
use App\Models\Country;
use App\Models\BuyerType;
use App\Models\AgencySupportService;
use App\Models\Property;
use App\Models\Media;
use App\Mail\NewRfpSubmitted; // Import your Mail class
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail; // Import Mail Facade
use Inertia\Inertia;

class RfpController extends Controller
{
    /**
     * Show the dynamic public RFP form.
     */
    public function index()
    {
        // 1. Gather lookups to hydrate dropdowns and shortlist matching
        $countries = Country::orderBy('name', 'asc')->get(['id', 'name'])->toArray();
        $buyerTypes = BuyerType::orderBy('name', 'asc')->get(['id', 'name'])->toArray();
        $agencyServices = AgencySupportService::orderBy('sort_order', 'asc')->get(['id', 'name'])->toArray();

        // 2. Load properties so we can cross-reference the client's shortlisted slugs
        $properties = Property::where('is_active', true)
            ->with(['portfolioCategory', 'country'])
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id, // Database ID needed for relation syncing
                    'slug' => $p->slug, // Slug needed to match local Pinia state
                    'name' => $p->name,
                    'city' => $p->city ?? '',
                    'country' => $p->country->name ?? '',
                    'image' => $p->featured_image_url ?? 'https://images.unsplash.com/photo-1566073771259-1a873a6a8bed?auto=format&fit=crop&w=800&q=80',
                    'portfolioCategory' => $p->portfolioCategory->name ?? ''
                ];
            });

        return Inertia::render('Rfp', [
            'countries' => $countries,
            'buyerTypes' => $buyerTypes,
            'agencyServices' => $agencyServices,
            'properties' => $properties
        ]);
    }

    /**
     * Public store endpoint wrapped to trigger the admin email notification.
     */
    public function store(StoreRfpSubmissionRequest $request)
    {
        $validated = $request->validated();
        $attachmentUrl = null;

        // 1. Generate unique serial reference number
        $latest = RfpSubmission::latest('id')->first();
        $nextId = $latest ? $latest->id + 1 : 1;
        $referenceNumber = 'RFP-' . date('Y') . '-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);

        // 2. Resolve R2 attachment file URL
        if ($request->filled('attachment_id')) {
            $media = Media::find($request->input('attachment_id'));
            if ($media) {
                $attachmentUrl = $media->url;
            }
        }

        DB::beginTransaction();
        try {
            // 3. Create the core RFP submission
            $rfp = RfpSubmission::create(array_merge($validated, [
                'reference_number' => $referenceNumber,
                'attachment_url' => $attachmentUrl,
            ]));

            // 4. Sync shortlisted properties pivot
            $rfp->properties()->sync($validated['properties']);

            // 5. Sync requested agency support services pivot
            if (!empty($validated['agency_services'])) {
                $rfp->agencyServices()->sync($validated['agency_services']);
            }

            DB::commit();

            // 6. Send the Email Notification to hello@hmphospitality.co
            Mail::to('hello@hmphospitality.co')->send(new NewRfpSubmitted($rfp));

            return redirect()->back()->with('success', [
                'ref' => $referenceNumber,
                'ok' => true
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors([
                'submission' => 'An error occurred while compiling your RFP. Please try again.'
            ]);
        }
    }
}

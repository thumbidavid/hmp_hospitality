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
use App\Mail\NewRfpSubmitted;
use App\Mail\RfpClientConfirmation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class RfpController extends Controller
{
    /**
     * Show the dynamic public RFP form.
     */
    public function index()
    {
        $countries = Country::orderBy('name', 'asc')->get(['id', 'name'])->toArray();
        $buyerTypes = BuyerType::orderBy('name', 'asc')->get(['id', 'name'])->toArray();
        $agencyServices = AgencySupportService::orderBy('sort_order', 'asc')->get(['id', 'name'])->toArray();

        $properties = Property::where('is_active', true)
            ->with(['portfolioCategory', 'country'])
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'slug' => $p->slug,
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
     * Public store endpoint wrapped to trigger the admin and client emails.
     */
    public function store(StoreRfpSubmissionRequest $request)
    {
        $validated = $request->validated();
        $attachmentUrl = null;

        if ($request->filled('attachment_id')) {
            $media = Media::find($request->input('attachment_id'));
            if ($media) {
                $attachmentUrl = $media->url;
            }
        }

        DB::beginTransaction();
        try {
            $latest = RfpSubmission::latest('id')->first();
            $nextId = $latest ? $latest->id + 1 : 1;
            $referenceNumber = 'RFP-' . date('Y') . '-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);

            $rfp = RfpSubmission::create(array_merge($validated, [
                'reference_number' => $referenceNumber,
                'attachment_url' => $attachmentUrl,
            ]));

            if (!empty($validated['properties'])) {
                $rfp->properties()->sync($validated['properties']);
            }

            if (!empty($validated['agency_services'])) {
                $rfp->agencyServices()->sync($validated['agency_services']);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->back()->withErrors([
                'submission' => 'An error occurred while compiling your RFP: ' . $e->getMessage()
            ]);
        }

        // Eager load full relations for the email templates
        $rfp->load(['properties.country', 'agencyServices', 'buyerType', 'buyerCountry']);

        // Dispatch Mail Notifications
        try {
            Mail::to('hello@hmphospitality.co')->send(new NewRfpSubmitted($rfp));
            Mail::to($rfp->email)->send(new RfpClientConfirmation($rfp));
        } catch (\Throwable $e) {
            // Fail safely without disrupting the client response
        }

        return redirect()->back()->with('success', [
            'ref' => $rfp->reference_number,
            'ok' => true
        ]);
    }
}

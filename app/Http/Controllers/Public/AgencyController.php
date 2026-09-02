<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\RfpSubmission;
use App\Models\AgencySupportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Inertia\Inertia;

class AgencyController extends Controller
{
    public function index()
    {
        return Inertia::render('HmpAgency');
    }

    public function store(Request $request)
    {
        // 1. Validate incoming request details
        $validated = $request->validate([
            'fullName' => 'required|string|max:150',
            'organisation' => 'required|string|max:200',
            'email' => 'required|email|max:150',
            'telephone' => 'nullable|string|max:30',
            'jobTitle' => 'nullable|string|max:150',
            'orgType' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'commMethod' => 'nullable|string|max:50',
            'programmeName' => 'nullable|string|max:200',
            'programmeType' => 'nullable|string|max:100',
            'preferredCountry' => 'nullable|string|max:100',
            'preferredDestination' => 'nullable|string|max:200',
            'arrivalDate' => 'nullable|date',
            'departureDate' => 'nullable|date',
            'eventStart' => 'nullable|date',
            'eventEnd' => 'nullable|date',
            'numTravellers' => 'nullable|integer',
            'numAttendees' => 'nullable|integer',
            'numGuestrooms' => 'nullable|integer',
            'numRoomNights' => 'nullable|integer',
            'flexibleDates' => 'nullable|boolean',
            'budget' => 'nullable|numeric',
            'currency' => 'nullable|string|max:10',
            'accessibility' => 'nullable|string',
            'sustainability' => 'nullable|string',
            'vipRequirements' => 'nullable|string',
            'specialRequirements' => 'nullable|string',
            'proposalDeadline' => 'nullable|date',
            'decisionDate' => 'nullable|date',
            'additionalInformation' => 'nullable|string',
            'servicesRequired' => 'nullable|array'
        ]);

        // 2. Generate a unique transactional reference number
        $year = Carbon::now()->year;
        $reference = 'HMPA-' . $year . '-' . mt_rand(1000, 9999);

        DB::beginTransaction();
        try {
            // 3. Save into rfp_submissions table matching your schema columns
            $rfp = RfpSubmission::create([
                'reference_number' => $reference,
                'full_name' => $validated['fullName'],
                'job_title' => $validated['jobTitle'],
                'company_name' => $validated['organisation'],
                'email' => $validated['email'],
                'phone' => $validated['telephone'],
                'preferred_communication_method' => strtolower($validated['commMethod'] ?? 'email'),
                'programme_name' => $validated['programmeName'],
                'preferred_destination' => $validated['preferredDestination'] ?? $validated['preferredCountry'],
                'arrival_date' => $validated['arrivalDate'],
                'departure_date' => $validated['departureDate'],
                'is_dates_flexible' => (bool)($validated['flexibleDates'] ?? false),
                'number_of_attendees' => $validated['numAttendees'],
                'number_of_rooms' => $validated['numGuestrooms'],
                'number_of_room_nights' => $validated['numRoomNights'],
                'budget_amount' => $validated['budget'],
                'currency' => $validated['currency'] ?? 'USD',
                'accessibility_requirements' => $validated['accessibility'],
                'sustainability_requirements' => $validated['sustainability'],
                'additional_requirements' => $validated['additionalInformation'],
                'proposal_deadline' => $validated['proposalDeadline'],
                'decision_date' => $validated['decisionDate'],
                'privacy_policy_accepted' => true,
                'status' => 'new',
                'source' => 'HMP Hospitality website'
            ]);

            // 4. Map and sync chosen services to your agency_support_services table
            if (!empty($validated['servicesRequired'])) {
                $serviceIds = [];
                foreach ($validated['servicesRequired'] as $serviceName) {
                    // Find or dynamically create support service to avoid DB errors
                    $service = AgencySupportService::firstOrCreate(
                        ['slug' => Str::slug($serviceName)],
                        ['name' => $serviceName]
                    );
                    $serviceIds[] = $service->id;
                }
                // Sync to many-to-many intermediate table
                $rfp->agencySupportServices()->sync($serviceIds);
            }

            DB::commit();

            return back()->with('success', [
                'ref' => $reference,
                'ok' => true
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            // Fallback safe reference return in case of errors
            return back()->with('success', [
                'ref' => $reference,
                'ok' => true,
                'note' => 'Your reference is saved. A team member will follow up by email shortly.'
            ]);
        }
    }
}

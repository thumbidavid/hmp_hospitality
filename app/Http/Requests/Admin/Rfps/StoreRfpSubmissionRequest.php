<?php

namespace App\Http\Requests\Admin\Rfps;

use Illuminate\Foundation\Http\FormRequest;

class StoreRfpSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Publicly accessible anonymous submission
    }

    public function rules(): array
    {
        return [
            // Step 1: Profile
            'full_name' => ['required', 'string', 'max:150'],
            'job_title' => ['nullable', 'string', 'max:150'],
            'company_name' => ['nullable', 'string', 'max:200'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'buyer_country_id' => ['required', 'exists:countries,id'],
            'buyer_type_id' => ['required', 'exists:buyer_types,id'],
            'preferred_communication_method' => ['required', 'string', 'in:email,telephone,video_call,whatsapp'],

            // Step 2: Requirement Type
            'requirement_type' => [
                'required',
                'string',
                'in:business_travel,group_accommodation,conference_or_meeting,incentive_programme,association_event,government_ngo_programme,leisure_group,long_stay_accommodation,venue_only_event,destination_enquiry'
            ],

            // Step 3: Programme Info
            'programme_name' => ['nullable', 'string', 'max:200'],
            'preferred_destination' => ['nullable', 'string', 'max:200'],
            'destination_id' => ['nullable', 'exists:destinations,id'],
            'is_destination_flexible' => ['nullable', 'boolean'],

            'arrival_date' => ['nullable', 'date'],
            'departure_date' => ['nullable', 'date', 'after_or_equal:arrival_date'],
            'is_dates_flexible' => ['nullable', 'boolean'],

            'number_of_attendees' => ['nullable', 'integer', 'min:0'],
            'number_of_rooms' => ['nullable', 'integer', 'min:0'],
            'number_of_room_nights' => ['nullable', 'integer', 'min:0'],

            'meeting_room_requirements' => ['nullable', 'string'],
            'venue_capacity' => ['nullable', 'integer', 'min:0'],
            'fnb_requirements' => ['nullable', 'string'],
            'transfer_airport_requirements' => ['nullable', 'string'],

            'budget_amount' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'in:USD,KES,ZAR,EUR,GBP,NGN,MAD'],

            'accessibility_requirements' => ['nullable', 'string'],
            'sustainability_requirements' => ['nullable', 'string'],
            'additional_requirements' => ['nullable', 'string'],

            'proposal_deadline' => ['nullable', 'date'],
            'decision_date' => ['nullable', 'date'],
            'attachment_id' => ['nullable', 'integer', 'exists:media,id'], // Media record reference

            // Step 4: Shortlist & Consent
            'privacy_policy_accepted' => ['required', 'boolean', 'accepted'],
            'properties' => ['required', 'array', 'min:1'], // Must contain at least one shortlisted property ID
            'properties.*' => ['integer', 'exists:properties,id'],

            'agency_services' => ['nullable', 'array'],
            'agency_services.*' => ['integer', 'exists:agency_support_services,id'],
        ];
    }
}

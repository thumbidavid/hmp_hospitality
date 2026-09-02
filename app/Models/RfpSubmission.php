<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RfpSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_number',
        'full_name',
        'job_title',
        'company_name',
        'email',
        'phone',
        'buyer_country_id',
        'buyer_type_id',
        'preferred_communication_method',
        'requirement_type',
        'programme_name',
        'preferred_destination',
        'destination_id',
        'is_destination_flexible',
        'arrival_date',
        'departure_date',
        'is_dates_flexible',
        'number_of_attendees',
        'number_of_rooms',
        'number_of_room_nights',
        'meeting_room_requirements',
        'venue_capacity',
        'fnb_requirements',
        'transfer_airport_requirements',
        'budget_amount',
        'currency',
        'accessibility_requirements',
        'sustainability_requirements',
        'additional_requirements',
        'proposal_deadline',
        'decision_date',
        'attachment_url',
        'privacy_policy_accepted',
        'status',
        'source',
    ];

    protected $casts = [
        'is_destination_flexible' => 'boolean',
        'is_dates_flexible' => 'boolean',
        'privacy_policy_accepted' => 'boolean',
        'arrival_date' => 'date',
        'departure_date' => 'date',
        'proposal_deadline' => 'date',
        'decision_date' => 'date',
        'budget_amount' => 'decimal:2',
    ];

    public function buyerCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'buyer_country_id');
    }

    public function buyerType(): BelongsTo
    {
        return $this->belongsTo(BuyerType::class);
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function properties(): BelongsToMany
    {
        return $this->belongsToMany(Property::class, 'rfp_properties', 'rfp_id', 'property_id');
    }

    public function agencyServices(): BelongsToMany
    {
        return $this->belongsToMany(AgencySupportService::class, 'rfp_agency_support_services', 'rfp_id', 'agency_support_service_id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(RfpNote::class, 'rfp_id')->orderBy('created_at', 'desc');
    }
}

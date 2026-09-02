<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class AgencySupportService extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'sort_order',
    ];

    /**
     * Auto-slugify the name when saving if no slug is provided.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($service) {
            if (empty($service->slug)) {
                $service->slug = Str::slug($service->name);
            }
        });
    }

    /**
     * An agency service belongs to many RFPs (Pivot table: rfp_agency_support_services).
     */
    public function rfpSubmissions(): BelongsToMany
    {
        return $this->belongsToMany(RfpSubmission::class, 'rfp_agency_support_services');
    }
}

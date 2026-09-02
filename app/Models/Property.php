<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Property extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'portfolio_category_id',
        'destination_id',
        'country_id',
        'name',
        'slug',
        'tagline',
        'overview',
        'accommodation_details',
        'meetings_facilities_details',
        'dining_leisure_details',
        'sustainability_details',
        'city',
        'latitude',
        'longitude',
        'number_of_rooms',
        'max_event_capacity',
        'website_url',
        'featured_image_url',
        'is_featured',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($property) {
            if (empty($property->slug)) {
                $property->slug = Str::slug($property->name);
            }
        });
    }

    public function portfolioCategory(): BelongsTo
    {
        return $this->belongsTo(PortfolioCategory::class);
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function settings(): BelongsToMany
    {
        return $this->belongsToMany(Setting::class, 'property_settings');
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'property_amenities');
    }

    public function images(): HasMany
    {
        return $this->hasMany(PropertyImage::class)->orderBy('sort_order');
    }

    public function highlights(): HasMany
    {
        return $this->hasMany(ContentHighlight::class, 'subject_id')
            ->where('subject_type', 'property');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'subject_id')
            ->where('subject_type', 'property');
    }
}

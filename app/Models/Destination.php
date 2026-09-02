<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Destination extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'country_id',
        'name',
        'slug',
        'intro_description',
        'access_connectivity',
        'best_time_to_visit',
        'website_url',
        'hero_image_url',
        'is_featured',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($destination) {
            if (empty($destination->slug)) {
                $destination->slug = Str::slug($destination->name);
            }
        });
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    /**
     * Retrieve highlights representing reasons to visit or attractions.
     */
    public function highlights(): HasMany
    {
        return $this->hasMany(ContentHighlight::class, 'subject_id')
            ->where('subject_type', 'destination');
    }

    /**
     * Retrieve related downloadable marketing resources.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'subject_id')
            ->where('subject_type', 'destination');
    }
}

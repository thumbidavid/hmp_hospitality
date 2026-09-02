<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyImage extends Model
{
    public $timestamps = false; // Custom created_at timestamp used, no updated_at

    protected $fillable = [
        'property_id',
        'image_url',
        'caption',
        'is_cover',
        'sort_order',
        'created_at',
    ];

    protected $casts = [
        'is_cover' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($image) {
            $image->created_at = now();
        });
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}

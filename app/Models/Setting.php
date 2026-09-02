<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Setting extends Model
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
    ];

    /**
     * Auto-slugify the setting name when saving if no slug is provided.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($setting) {
            if (empty($setting->slug)) {
                $setting->slug = Str::slug($setting->name);
            }
        });
    }

    /**
     * A setting belongs to many properties (Pivot table: property_settings).
     */
    public function properties(): BelongsToMany
    {
        return $this->belongsToMany(Property::class, 'property_settings');
    }
}

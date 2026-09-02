<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class BuyerType extends Model
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
     * Auto-slugify the name when saving if no slug is provided.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($buyerType) {
            if (empty($buyerType->slug)) {
                $buyerType->slug = Str::slug($buyerType->name);
            }
        });
    }

    /**
     * A buyer type has many RFP submissions.
     */
    public function rfpSubmissions(): HasMany
    {
        return $this->hasMany(RfpSubmission::class);
    }
}

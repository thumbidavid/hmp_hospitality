<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RfpNote extends Model
{
    protected $fillable = [
        'rfp_id',
        'user_id',
        'note',
    ];

    public function rfpSubmission(): BelongsTo
    {
        return $this->belongsTo(RfpSubmission::class, 'rfp_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

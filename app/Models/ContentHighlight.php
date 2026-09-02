<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentHighlight extends Model
{
    protected $fillable = [
        'subject_type',
        'subject_id',
        'type',
        'text',
        'sort_order',
    ];
}

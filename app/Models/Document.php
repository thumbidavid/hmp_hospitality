<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'subject_type',
        'subject_id',
        'label',
        'file_url',
        'sort_order',
    ];
}

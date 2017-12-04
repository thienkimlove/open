<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Element extends Model
{
    protected $fillable = [
        'content_id',
        'social_id',
        'social_type',
        'social_level',
        'social_name',
        'social_parent',
        'social_status',
        'last_insight_updated',
        'json_data',
    ];

    public function content()
    {
        return $this->belongsTo(Content::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $fillable = [
        'user_id',
        'account_id',
        'content_id',

        'social_id',
        'social_name',
        'social_type',
        'status',

        //addition fields
        'social_account_id',
        'boosted_object_id',
        'buying_type',
        'created_time',
        'objective',
        'start_time',
        'stop_time',
        'updated_time',

    ];

    public function content()
    {
        return $this->belongsTo(Content::class);
    }

    public function sets()
    {
        return $this->hasMany(Set::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Set extends Model
{
    protected $fillable = [
        'user_id',
        'account_id',
        'content_id',
        'campaign_id',

        'social_id',
        'social_name',
        'social_type',
        'status',

        'social_account_id',
        'budget_remaining',
        'social_campaign_id',
        'created_time',
        'daily_budget',
        'destination_type',
        'end_time',
        'lifetime_budget',
        'lifetime_imps',
        'start_time',
        'updated_time',

    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function ads()
    {
        return $this->hasMany(Ad::class);
    }
}

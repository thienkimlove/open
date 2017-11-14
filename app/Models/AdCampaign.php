<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdCampaign extends Model
{
    protected $fillable = [
        'id',
        'ad_account_id',
        'user_id',
        'fb_account_id',
        'name',
    ];

    public function adAccount()
    {
        return $this->belongsTo(AdAccount::class, 'id', 'ad_account_id');
    }

    public function adSets()
    {
        return $this->hasMany(AdAdSet::class, 'ad_campaign_id', 'id');
    }
}

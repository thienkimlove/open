<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdAdSet extends Model
{
    public $table = 'ad_adsets';
    protected $fillable = [
        'id',
        'ad_campaign_id',
        'name',
    ];

    public function adCampaign()
    {
        return $this->belongsTo(AdCampaign::class, 'id', 'ad_campaign_id');
    }

    public function adAds()
    {
        return $this->hasMany(AdAd::class, 'ad_adset_id', 'id');
    }

}

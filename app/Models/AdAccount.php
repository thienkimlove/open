<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdAccount extends Model
{
    protected $fillable = [
        'id',
        'fb_account_id',
        'name'
    ];

    public function fbAccount()
    {
        return $this->belongsTo(FbAccount::class, 'id', 'fb_account_id');
    }

    public function adCampaigns()
    {
        return $this->hasMany(AdCampaign::class, 'ad_account_id', 'id');
    }
}

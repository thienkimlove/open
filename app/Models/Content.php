<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $fillable = [
        'user_id',
        'account_id',
        'social_id',
        'social_name',
        'social_type',
        'status',

        //attributes for account_level
        'amount_spent',
        'balance',
        'currency',
        'min_campaign_group_spend_cap',
        'min_daily_budget',
        'next_bill_date',
        'spend_cap',
        'last_report_run',
        'map_user_id'
    ];


    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }

    public function sets()
    {
        return $this->hasMany(Set::class);
    }

    public function ads()
    {
        return $this->hasMany(Ad::class);
    }


}

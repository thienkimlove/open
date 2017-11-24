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
        'last_report_run'
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

    public function getTotalInsight()
    {
        return Insight::where('content_id', $this->id)->where('social_type', config('system.social_type.facebook'))->where('object_type', config('system.insight.types.content'))->count();
    }

}

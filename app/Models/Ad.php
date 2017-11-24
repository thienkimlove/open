<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    protected $fillable = [
        'user_id',
        'account_id',
        'content_id',
        'campaign_id',
        'set_id',

        'social_id',
        'social_name',
        'social_type',
        'status',

        'social_account_id',
        'social_campaign_id',
        'social_adset_id',
        'created_time',
        'updated_time',
        'last_report_run'

    ];

    public function content()
    {
        return $this->belongsTo(Content::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}

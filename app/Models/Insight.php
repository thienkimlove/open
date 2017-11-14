<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Insight extends Model
{
    protected $fillable = [
        'content_id',
        'content_type',
        'date',

        'account_currency',
        'account_id',
        'account_name',

        'ad_id',
        'ad_name',
        'adset_id',
        'adset_name',

        'buying_type',

        'campaign_id',
        'campaign_name',

        'clicks',
        'cpc',
        'cpm',
        'cpp',
        'ctr',

        'impressions',
        'inline_link_click_ctr',
        'inline_link_clicks',
        'inline_post_engagement',
        'reach',

        'social_clicks',
        'social_impressions',
        'social_reach',
        'social_spend',
        'spend',

        'unique_clicks',
        'unique_ctr',
        'unique_inline_link_click_ctr',
        'unique_inline_link_clicks',
        'unique_link_clicks_ctr',
        'unique_social_clicks',
    ];
}

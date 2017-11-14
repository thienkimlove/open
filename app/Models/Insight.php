<?php

namespace App\Models;

use Carbon\Carbon;
use Sentinel;
use Illuminate\Database\Eloquent\Model;
use DataTables;

class Insight extends Model
{
    protected $fillable = [
        'user_id',
        'fb_account_id',
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

    public static function getDataTables($request)
    {
        $user = Sentinel::getUser();
        if ($user->isSuperAdmin()) {
            $insight = static::select('*');
        } else {
            $insight = static::select('*')->where('user_id', $user->id);
        }

        return DataTables::of($insight)
            ->filter(function ($query) use ($request) {
                if ($request->filled('user_id')) {
                    $query->where('user_id', $request->get('user_id'));
                }

                if ($request->filled('type')) {
                    $query->where('content_type', $request->get('type'));
                }

                if ($request->filled('date')) {
                    $dateRange = explode(' - ', $request->get('date'));
                    $query->whereDate('date', '>=', Carbon::createFromFormat('d/m/Y', $dateRange[0])->toDateString());
                    $query->whereDate('date', '<=', Carbon::createFromFormat('d/m/Y', $dateRange[1])->toDateString());
                }
            })->editColumn('content_type', function ($insight) {
                return config('system.insight.values.'.$insight->content_type);
            })->editColumn('spend', function ($insight) {
                return $insight->spend.$insight->account_currency;
            })
            ->make(true);
    }
}

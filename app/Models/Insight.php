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
        'account_id',
        'content_id',
        'campaign_id',
        'set_id',
        'ad_id',
        'date',
        'object_type',
        'social_type',

        'social_account_id',
        'social_campaign_id',
        'social_adset_id',
        'social_ad_id',

        'clicks',
        'impressions',
        'reach',
        'spend',

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function account()
    {
        return $this->belongsTo(Account::class);
    }

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
                    $query->where('object_type', $request->get('type'));
                }

                if ($request->filled('date')) {
                    $dateRange = explode(' - ', $request->get('date'));
                    $query->whereDate('date', '>=', Carbon::createFromFormat('d/m/Y', $dateRange[0])->toDateString());
                    $query->whereDate('date', '<=', Carbon::createFromFormat('d/m/Y', $dateRange[1])->toDateString());
                }
            })->editColumn('object_type', function ($insight) {
                return config('system.insight.values.'.$insight->object_type);
            })->editColumn('spend', function ($insight) {
                return $insight->spend."VND";
            })
            ->make(true);
    }
}

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
        'json',
        'result',
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

        $insight = static::with('content', 'ad', 'set', 'campaign', 'user', 'account');

        if ($user->isAdmin()) {
            //
        } elseif ($user->isManager()) {
            $insight->whereIn('user_id', $user->getAllUsersInGroup());
        } else {
            $insight->where('user_id', $user->id);
        }

        return DataTables::of($insight)
            ->filter(function ($query) use ($request) {
                if ($request->filled('user_id')) {
                    $query->where('user_id', $request->get('user_id'));
                }

                if ($request->filled('department_id')) {
                    $departmentId = $request->get('department_id');
                    $query->whereHas('user', function ($query) use ($departmentId) {
                       $query->where('department_id', $departmentId);
                    });
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
                return number_format($insight->spend)." đ";
            })
            ->editColumn('clicks', function ($insight) {
                return number_format($insight->clicks);
            })
            ->editColumn('impressions', function ($insight) {
                return number_format($insight->impressions);
            })
            ->make(true);
    }

    public function scopeObjectAd($query)
    {
        return $query->where('object_type', config('system.insight.types.ad'));
    }

    public function getJsonDataAttribute()
    {
        return json_decode($this->getAttribute('json'), true);
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function set()
    {
        return $this->belongsTo(Set::class);
    }

    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }

    public function content()
    {
        return $this->belongsTo(Content::class);
    }
}

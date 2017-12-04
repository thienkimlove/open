<?php

namespace App\Models;

use Sentinel;
use DataTables;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'social_id',
        'social_name',
        'social_type',
        'user_id',
        'api_token',
        'api_token_start_date',
        'status',
    ];

    protected $dates = ['api_token_start_date'];


    public static function getDataTables($request)
    {
        $account = static::select('*');


        return DataTables::of($account)
            ->filter(function ($query) use ($request) {
                if ($request->filled('social_name')) {
                    $query->where('social_name', 'like', '%' . $request->get('social_name') . '%');
                }


                if ($request->filled('social_type')) {
                    $query->where('social_type', $request->get('social_type'));
                }

                if ($request->filled('status')) {
                    $query->where('status', $request->get('status'));
                }
            })->editColumn('status', function ($account) {
                return $account->status ? '<i class="ion ion-checkmark-circled text-success"></i>' : '<i class="ion ion-close-circled text-danger"></i>';
            })->editColumn('social_type', function ($account) {
                return config('system.social_type_values.'.$account->social_type);
            })->editColumn('api_token_start_date', function ($account) {
                return $account->api_token_start_date->addDays(55)->toDateString();
            })->rawColumns(['status'])
            ->make(true);
    }
}

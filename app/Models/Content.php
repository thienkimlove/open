<?php

namespace App\Models;

use Sentinel;
use DataTables;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $fillable = [
        'account_id',
        'social_id',
        'social_name',
        'social_type',
        'status',
        'user_id',
        //attributes for account_level

        'currency',
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


    public static function getDataTables($request)
    {
        $content = static::select('*');


        return DataTables::of($content)
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
            })->editColumn('user_id', function ($account) {
                return isset($account->user) ? $account->user->name : 'Not Assign Yet';
            })->rawColumns(['status'])
            ->make(true);
    }

}

<?php

namespace App\Models;

use Sentinel;
use DataTables;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'date',
        'element_id',
        'cost_per_result',
        'result',
        'spend',
        'json_data',
    ];

    public function element()
    {
        return $this->belongsTo(Element::class);
    }

    public static function getDataTables($request)
    {
        $report = static::select('*')->with('element');


        return DataTables::of($report)
            ->filter(function ($query) use ($request) {
                if ($request->filled('user_id')) {
                    $query->whereHas('element', function ($q1) use ($request) {
                        $q1->whereHas('content', function ($q2) use ($request) {
                            $q2->where('map_user_id', $request->get('user_id'));
                        });
                    });

                }

                if ($request->filled('department_id')) {
                    $query->whereHas('element', function ($q1) use ($request) {
                        $q1->whereHas('content', function ($q2) use ($request) {
                            $q2->whereHas('mapUser', function ($q3) use ($request) {
                                $q3->where('department_id', $request->get('department_id'));
                            });
                        });
                    });

                }

                if ($request->filled('type')) {
                    $query->whereHas('element', function($q) use($request) {
                        $q->where('social_level', $request->get('type'));
                    });
                }

                if ($request->filled('date')) {
                    $dateRange = explode(' - ', $request->get('date'));
                    $query->whereDate('date', '>=', Carbon::createFromFormat('d/m/Y', $dateRange[0])->toDateString());
                    $query->whereDate('date', '<=', Carbon::createFromFormat('d/m/Y', $dateRange[1])->toDateString());
                }
            })->addColumn('social_name', function ($report) {
                return $report->element->social_name;
            })->addColumn('social_id', function ($report) {
                return $report->element->social_id;
            })->addColumn('social_level', function ($report) {
                return config('system.insight.values.'.$report->element->social_level);
            })->addColumn('social_type', function ($report) {
                return config('system.social_type_values.'.$report->element->social_type);
            })
            ->addColumn('checkbox', function ($report) {
                return '<input type="checkbox" data-id="' . $report->id . '">';
            })
            ->rawColumns(['checkbox'])
            ->make(true);
    }
}

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
        $report = static::select('*')->with('element.content.user.department');

        $currentUser = Sentinel::getUser();

        if ($currentUser->isAdmin()) {
            $report->whereHas('element', function ($q1) use ($request) {
                $q1->whereHas('content', function ($q2) use ($request) {
                    $q2->whereNotNull('user_id');
                });
            });
        } elseif ($currentUser->isManager()) {
            $report->whereHas('element', function ($q1) use ($request, $currentUser) {
                $q1->whereHas('content', function ($q2) use ($request, $currentUser) {
                    $q2->whereIn('user_id', $currentUser->getAllUsersInGroup());
                });
            });
        } else {
            $report->whereHas('element', function ($q1) use ($request, $currentUser) {
                $q1->whereHas('content', function ($q2) use ($request, $currentUser) {
                    $q2->where('user_id', $currentUser->id);
                });
            });
        }

        return DataTables::of($report)
            ->filter(function ($query) use ($request) {
                if ($request->filled('user_id')) {
                    $query->whereHas('element', function ($q1) use ($request) {
                        $q1->whereHas('content', function ($q2) use ($request) {
                            $q2->where('user_id', $request->get('user_id'));
                        });
                    });

                }

                if ($request->filled('department_id')) {
                    $query->whereHas('element', function ($q1) use ($request) {
                        $q1->whereHas('content', function ($q2) use ($request) {
                            $q2->whereHas('user', function ($q3) use ($request) {
                                $q3->where('department_id', $request->get('department_id'));
                            });
                        });
                    });

                }


                if ($request->filled('content_id')) {
                    $query->whereHas('element', function($q) use($request) {
                        $q->where('content_id', $request->get('content_id'));
                    });
                }

                if ($request->filled('date')) {
                    $dateRange = explode('-', $request->get('date'));
                    $query->whereDate('date', '>=', Carbon::createFromFormat('d/m/Y', trim($dateRange[0]))->toDateString());
                    $query->whereDate('date', '<=', Carbon::createFromFormat('d/m/Y', trim($dateRange[1]))->toDateString());
                }
            })
            ->addColumn('social_name', function ($report) {
                return $report->element->social_name;
            })
            ->addColumn('social_id', function ($report) {
                return $report->element->social_id;
            })
            ->addColumn('username', function ($report) {
                return isset($report->element->content->user->name) ? $report->element->content->user->name : '';
            })
            ->addColumn('department', function ($report) {
                return isset($report->element->content->user->department)? $report->element->content->user->department->name : '';
            })
            ->addColumn('social_type', function ($report) {
                return config('system.social_type_values.'.$report->element->social_type);
            })
            ->addColumn('checkbox', function ($report) {
                return '<input type="checkbox" data-id="' . $report->id . '">';
            })
            ->editColumn('spend', function ($report) {
                return $report->getCorrectSpend();
            })
            ->editColumn('cost_per_result', function ($report) {
                return $report->getCorrectSpend();
            })
            ->rawColumns(['checkbox'])
            ->make(true);
    }

    public function getCorrectSpend()
    {
        return ($this->element->content->currency=='USD') ? 23000*$this->spend : $this->spend;
    }

    public function getCorrectCostPerResult()
    {
        return ($this->element->content->currency=='USD') ? 23000*$this->cost_per_result : $this->cost_per_result;
    }

    public static function exportToExcel($request)
    {
        ini_set('memory_limit', '2048M');

        $query = static::with('element');

        if ($request->filled('filter_user_id')) {
            $query->whereHas('element', function ($q1) use ($request) {
                $q1->whereHas('content', function ($q2) use ($request) {
                    $q2->where('user_id', $request->get('filter_user_id'));
                });
            });

        }

        if ($request->filled('filter_department_id')) {
            $query->whereHas('element', function ($q1) use ($request) {
                $q1->whereHas('content', function ($q2) use ($request) {
                    $q2->whereHas('user', function ($q3) use ($request) {
                        $q3->where('department_id', $request->get('filter_department_id'));
                    });
                });
            });

        }



        if ($request->filled('filter_date')) {
            $dateRange = explode('-', $request->get('filter_date'));
            $query->whereDate('date', '>=', Carbon::createFromFormat('d/m/Y', trim($dateRange[0]))->toDateString());
            $query->whereDate('date', '<=', Carbon::createFromFormat('d/m/Y', trim($dateRange[1]))->toDateString());
        }

        $reports = $query->get();

        return (new static())->createExcellFile($reports);
    }

    public function createExcellFile($reports)
    {
        $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
        $objPHPExcel = $objReader->load(resource_path('templates/results.xlsx'));

        $row = 2;
        foreach ($reports as $report) {



            $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $row - 1)
                ->setCellValue('B'.$row, $report->date)
                ->setCellValue('C'.$row, $report->element->social_id)
                ->setCellValue('D'.$row, $report->element->social_name)
                ->setCellValue('E'.$row, config('system.insight.values.'.$report->element->social_level))
                ->setCellValue('F'.$row, config('system.social_type_values.'.$report->element->social_type))
                ->setCellValue('G'.$row, $report->result)
                ->setCellValue('H'.$row, $report->getCorrectCostPerResult())
                ->setCellValue('I'.$row, $report->getCorrectSpend());

            $row++;
        }

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

        $path = 'reports_'.date('Y_m_d_His').'.xlsx';

        $objWriter->save(storage_path('app/public/' . $path));

        return redirect('/storage/' . $path);
    }
}

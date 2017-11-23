<?php

namespace App\Models;

use DataTables;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasActionColumn;

    protected $fillable = ['code', 'name', 'status'];

    public function getDatatables($request)
    {
        $departments = static::query();

        return DataTables::of($departments)
            ->filter(function ($query) use ($request) {
                if ($request->filled('name')) {
                    $query->where('name', 'like', '%' . $request->get('name') . '%');
                }
            })
            ->addColumn('action', function ($department) {
                return $this->generateActionColumn($department);
            })
            ->editColumn('status', function ($department) {
                return $department->status ? '<i class="ion ion-checkmark-circled text-success"></i>': '<i class="ion ion-close-circled text-danger"></i>';
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }

    public function getActionColumnPermissions($department)
    {
        $actions['departments.edit'] = '<a class="table-action-btn" title="Sửa phòng ban" href="' . route('departments.edit', $department->id) . '"><i class="fa fa-pencil text-warning"></i></a>';

        return $actions;
    }
}

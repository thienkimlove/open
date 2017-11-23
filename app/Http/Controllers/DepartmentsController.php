<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Facades\App\Models\Department;
use Illuminate\Support\Facades\DB;

class DepartmentsController extends Controller
{
    public function index()
    {
        return view('departments.index');
    }

    public function datatables(Request $request)
    {
        return Department::getDatatables($request);
    }

    public function create()
    {
        return view('departments.create');
    }

    public function edit($id)
    {
        $department = Department::findOrFail($id);

        return view('departments.edit', compact('department'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        if (! $request->filled('status')) {
            $request->merge(['status' => 0]);
        }

        DB::beginTransaction();

        try {
            Department::create($request->all())->save();

            flash()->success('Thành công', 'Đã cập nhật');

            DB::commit();
        } catch (\Exception $e) {
            flash()->error('Error', $e->getMessage());

            DB::rollback();
        }

        return redirect()->route('departments.index');
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        if (! $request->filled('status')) {
            $request->merge(['status' => 0]);
        }

        DB::beginTransaction();

        try {
            $department = Department::findOrFail($id);

            $department->fill($request->all())->save();

            flash()->success('Thành công', 'Đã cập nhật');

            DB::commit();
        } catch (\Exception $e) {
            flash()->error('Error', $e->getMessage());

            DB::rollback();
        }

        return back();
    }
}

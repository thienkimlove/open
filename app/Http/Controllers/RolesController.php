<?php

namespace App\Http\Controllers;

use Facades\App\Models\Role;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Sentinel;
use Illuminate\Http\Request;

class RolesController extends Controller
{
    public function index()
    {
        return view('roles.index');
    }

    public function create()
    {
        return view('roles.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
        ]);

        Sentinel::getRoleRepository()->createModel()->create($request->all());

        flash()->success('Success!', 'Role successfully created.');

        return redirect()->route('roles.index');
    }

    public function show($id)
    {
        $role = Sentinel::findRoleById($id);

        if (! $role) {
            throw new ModelNotFoundException('Role not found.');
        }

        return view('roles.edit', compact('role'));
    }

    public function edit($id)
    {
        $role = Sentinel::findRoleById($id);

        if (! $role) {
            throw new ModelNotFoundException('Role not found.');
        }

        return view('roles.edit', compact('role'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
        ]);

        $role = Sentinel::findRoleById($id);

        if (! $role) {
            throw new ModelNotFoundException('Role not found.');
        }

        $role->update($request->all());

        flash()->success('Success!', 'Role successfully updated.');

        return redirect()->route('roles.index');
    }

    public function destroy($id)
    {
        $role = Sentinel::findRoleById($id);

        if (! $role) {
            throw new ModelNotFoundException('Role not found.');
        }

        $role->delete();

        flash()->success('Success!', 'Role successfully deleted.');

        return response()->json();
    }

    public function dataTables(Request $request)
    {
        return Role::getDataTables($request);
    }
}

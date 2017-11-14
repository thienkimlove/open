<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Sentinel;
use Illuminate\Http\Request;
use App\Models\Permission;

class RolePermissionsController extends Controller
{
    public function index($roleId)
    {
        $role = Sentinel::findRoleById($roleId);

        if (! $role) {
            throw new ModelNotFoundException('Role not found.');
        }

        $permissions = Permission::all();

        return view('rolePermissions.index', compact('role', 'permissions'));
    }

    public function update(Request $request, $roleId)
    {
        $role = Sentinel::findRoleById($roleId);

        if (! $role) {
            throw new ModelNotFoundException('Role not found.');
        }

        $role->grantPermissions($request->input('permissions', []));

        flash()->success('Success!', 'Role Permissions successfully updated.');

        return redirect()->route('rolePermissions.index', $roleId);
    }
}

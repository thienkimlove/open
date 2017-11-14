<?php

namespace App\Http\Controllers;

use Sentinel;
use Illuminate\Http\Request;
use Facades\App\Models\Permission;

class UserPermissionsController extends Controller
{
    public function index($userId)
    {
        $user = Sentinel::findById($userId);

        if (! $user) {
            throw new ModelNotFoundException('User not found.');
        }

        $permissions = Permission::all();

        return view('userPermissions.index', compact('user', 'permissions'));
    }

    public function update(Request $request, $userId)
    {
        $user = Sentinel::findById($userId);

        if (! $user) {
            throw new ModelNotFoundException('User not found.');
        }

        $user->grantPermissions($request->get('permissions', []));

        flash()->success('Success!', 'User Permissions successfully updated.');

        return redirect()->route('userPermissions.index', $userId);
    }
}

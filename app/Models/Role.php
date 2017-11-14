<?php

namespace App\Models;

use DataTables;
use Cartalyst\Sentinel\Roles\EloquentRole;
use Cviebrock\EloquentSluggable\Sluggable;

class Role extends EloquentRole
{
    use Sluggable, HasActionColumn;

    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'name',
                'onUpdate' => true,
            ]
        ];
    }

    public function delete()
    {
        $this->users()->detach();

        return parent::delete();
    }

    public function grantPermissions(array $permissions)
    {
        $this->permissions = array_map(function ($value) {
            return ($value == 1) ? true : false;
        }, $permissions);

        $this->save();
    }

    public function getDataTables()
    {
        $roles = Role::select([
            'id', 'slug', 'name', 'permissions'
        ]);

        return DataTables::of($roles)
            ->addColumn('action', function ($role) {
                return $this->generateActionColumn($role);
            })
            ->make(true);
    }

    protected function getActionColumnPermissions($role)
    {
        return [
            'rolePermissions.index' => '<a class="table-action-btn" href="' . route('rolePermissions.index', $role->id) . '"><i class="fa fa-lock text-warning"></i></a>',
            'roles.edit' => '<a class="table-action-btn" href="' . route('roles.edit', $role->id) . '"><i class="fa fa-pencil text-success"></i></a>',
            'roles.destroy' => '<a class="table-action-btn" id="btn-delete-' . $role->id . '" data-url="' . route('roles.destroy', $role->id) . '" href="javascript:;"><i class="fa fa-trash-o text-danger"></i></a>',
        ];
    }
}

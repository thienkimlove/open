<?php

namespace App\Models;

use Sentinel;
use DataTables;
use Activation;
use Illuminate\Auth\Authenticatable;
use Cartalyst\Sentinel\Users\EloquentUser;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class User extends EloquentUser implements
    AuthenticatableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'status',
        'permissions',
        'last_login',
        'is_superadmin',
        'phone',
        'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_users', 'user_id', 'role_id');
    }


    public function fbAccounts()
    {
        return $this->hasMany(FbAccount::class, 'user_id', 'id');
    }


    public static function getDataTables($request)
    {
        $user = static::select('id', 'name', 'email', 'status', 'created_at')->with('roles');

        return DataTables::of($user)
            ->filter(function ($query) use ($request) {
                if ($request->filled('name')) {
                    $query->where('name', 'like', '%' . $request->get('name') . '%');
                }

                if ($request->filled('email')) {
                    $query->where('email', 'like', '%' . $request->get('email') . '%');
                }

                if ($request->filled('role_id')) {
                    $query->whereHas('roles', function ($q) use ($request) {
                        return $q->where('id', $request->get('role_id'));
                    });
                }


                if ($request->filled('status')) {
                    $query->where('status', $request->get('status'));
                }
            })
            ->editColumn('status', function ($user) {
                return $user->status ? '<i class="ion ion-checkmark-circled text-success"></i>' : '<i class="ion ion-close-circled text-danger"></i>';
            })
            ->addColumn('roles', function ($user) {
                $roles = '';

                foreach ($user->roles as $role) {
                    $roles .= '&nbsp;&nbsp;<span style="background-color: #e3e3e3">' . $role->name . '</span>';
                }

                return $roles;
            })
            ->addColumn('action', function ($user) {
                return '<a class="table-action-btn" href="' . route('userPermissions.index', $user->id) . '" title="Phân quyền theo permission"><i class="fa fa-lock text-warning"></i></a>
                        <a class="table-action-btn" title="Chỉnh sửa người dùng" href="' . route('users.edit', $user->id) . '"><i class="fa fa-pencil text-success"></i></a>';
            })
            ->rawColumns(['roles', 'status', 'action', 'email', 'name'])
            ->make(true);
    }

    public function hasAccess($permissions)
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return parent::hasAccess($permissions);
    }

    public function hasAnyAccess($permissions)
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return parent::hasAnyAccess($permissions);
    }

    public function isSuperAdmin()
    {
        return (bool)$this->is_superadmin;
    }

    public function update(array $attributes = [], array $options = [])
    {
        if (empty($attributes['password'])) {
            unset($attributes['password']);
        }

        Sentinel::update($this, $attributes);

        $status = isset($attributes['status']) ? $attributes['status'] : false;

        $this->setActivation($status)
            ->updateRoles($this->getRoleIds($attributes));

        return $this;
    }

    protected function getRoleIds(array $attributes)
    {
        return isset($attributes['roles']) ? $attributes['roles'] : [];
    }

    protected function getActivationStatus(array $attributes)
    {
        return isset($attributes['completed']) ? (bool)$attributes['completed'] : false;
    }

    public function setActivation($completed)
    {
        if ($completed && !$this->isCompleted()) {
            return $this->completeActivation();
        }

        if (!$completed) {
            Activation::remove($this);
        }

        return $this;
    }

    public function isCompleted()
    {
        return Activation::completed($this);
    }

    public function completeActivation()
    {
        $activation = Activation::exists($this);

        if (!$activation) {
            $activation = Activation::create($this);
        }

        Activation::complete($this, $activation->code);

        return $this;
    }

    public function updateRoles($rolesId)
    {
        $this->roles()->sync($rolesId);

        return $this;
    }

    public function grantPermissions($permissions)
    {
        foreach ($permissions as $permission => $value) {
            $this->grantPermission($permission, $value);
        }

        $this->save();

        return $this;
    }

    protected function grantPermission($permission, $value)
    {
        return $this->permissionIsInherit($value)
            ? $this->removePermission($permission)
            : $this->updatePermission($permission, (bool)$value, true);
    }

    private function permissionIsInherit($value)
    {
        return $value == -1;
    }
}

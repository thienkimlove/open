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
        'password',
        'department_id',
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


    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    public function contents()
    {
        return $this->hasMany(Content::class);
    }


    public static function getDataTables($request)
    {
        $user = static::select('id', 'name', 'email', 'status', 'created_at', 'department_id')->with('roles', 'department', 'contents');

        $currentUser = Sentinel::getUser();

        if ($currentUser->isAdmin()) {
            //
        } elseif ($currentUser->isManager()) {
            $user->whereIn('id', $currentUser->getAllUsersInGroup());
        }

        return DataTables::of($user)
            ->filter(function ($query) use ($request) {
                if ($request->filled('name')) {
                    $query->where('name', 'like', '%' . $request->get('name') . '%');
                }

                if ($request->filled('email')) {
                    $query->where('email', 'like', '%' . $request->get('email') . '%');
                }

                if ($request->filled('department_id')) {
                    $query->where('department_id', $request->get('department_id'));
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
            ->addColumn('contents', function ($user) {
                $contents = '';

                foreach ($user->contents as $content) {
                    $contents .= '&nbsp;&nbsp;<span style="background-color: #e3e3e3">' . $content->social_name . '</span>';
                }

                return $contents;
            })
            ->addColumn('department_name', function ($user) {
                return $user->department ? $user->department->name : '';
            })
            ->addColumn('action', function ($user) use ($currentUser) {
                if ($currentUser->id != $user->id) {
                    return '<a class="table-action-btn" title="Chỉnh sửa người dùng" href="' . route('users.edit', $user->id) . '"><i class="fa fa-pencil text-success"></i></a>';
                } else {
                    return 'This is you!';
                }

            })
            ->rawColumns(['roles', 'contents', 'status', 'action', 'email', 'name'])
            ->make(true);
    }

    public function hasAccess($permissions)
    {
        if ($this->isAdmin()) {
            return true;
        }

        return parent::hasAccess($permissions);
    }

    public function hasAnyAccess($permissions)
    {
        if ($this->isAdmin()) {
            return true;
        }

        return parent::hasAnyAccess($permissions);
    }

    public static function create(array $attributes = [])
    {
        $static = new static();

        return Sentinel::register(
            array_merge($attributes, [
                'password' => bcrypt(str_random(32)),
            ]), true)
            ->setActivation(true)
            ->updateRoles($static->getRoleIds($attributes));
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

    public function isStaff()
    {
        return $this->checkRole('nhan-vien');
    }

    public function isManager()
    {
        return $this->checkRole('truong-phong');
    }

    public function isAdmin()
    {
        return $this->checkRole('admin');
    }

    public function checkRole($slug)
    {
        $roles = $this->roles->pluck('slug')->toArray();

        if (in_array($slug, $roles)) {
            return true;
        }

        return false;
    }

    public function getAllUsersInGroup()
    {
        if ($this->isAdmin()) {
            return static::where('status', 1)->pluck('id')->all();
        }

        if ($this->isManager()) {
            return static::where('status', 1)->where('department_id', $this->department_id)->pluck('id')->all();
        }

        return [$this->id];
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}

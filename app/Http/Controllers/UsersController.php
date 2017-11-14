<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Sentinel;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;

class UsersController extends Controller
{

    public $model = 'users';

    public $validator = [
        'name' => 'required',
        'email' => 'required|unique:users',
    ];


    public function index()
    {
        return view('users.index');
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(UserRequest $request)
    {
        $request->store();

        flash()->success('Success!', 'User successfully created.');

        return redirect()->route('users.index');
    }

    public function edit($id)
    {
        $user = Sentinel::findById($id);

        if (! $user) {
            throw new ModelNotFoundException('User not found.');
        }

        return view('users.edit', compact('user'));
    }

    public function update(UserRequest $request, $id)
    {
        $user = Sentinel::findById($id);

        if (! $user) {
            throw new ModelNotFoundException('User not found.');
        }

        if (! isset($request->status)) {
            $request->merge([
                'status' => 0,
            ]);
        }

        $user->update($request->all());

        flash()->success('Thành công', 'Cập nhật thành công!');

        return redirect()->route('users.edit', $id);
    }

    public function dataTables(Request $request)
    {
        return User::getDataTables($request);
    }

}
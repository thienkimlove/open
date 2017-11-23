<?php

use Illuminate\Database\Seeder;
use Hashids\Hashids;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $devEmail = file(resource_path('users/list.txt'), FILE_IGNORE_NEW_LINES);

        $sentinel = app('Cartalyst\Sentinel\Sentinel');

        foreach ($devEmail as $email) {

            $countUser = DB::table('users')->where('email', $email)->count();

            if ($countUser == 0) {
                $sentinel->registerAndActivate([
                    'name' => $email,
                    'email' => $email,
                    'password' => 'secret',
                    'is_superadmin' => true,
                ]);
            }
            else {
                $user = \App\Models\User::where('email', $email)->first();
                $user->setActivation(true);
            }
        }

        $roles = [
            'Admin', 'Trưởng phòng', 'Nhân viên',
        ];

        foreach ($roles as $role) {
            $countUser = DB::table('roles')->where('name', $role)->count();

            if ($countUser == 0) {
                \App\Models\Role::create([
                    'name' => $role,
                ]);
            }
        }
    }
}

<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
Route::get('login', 'BasicController@redirectToSSO')->name('login');
Route::get('logout', ['uses' => 'BasicController@logout', 'as' => 'logout']);
Route::get('callback', 'BasicController@handleSSOCallback')->name('callback');
Route::get('notice', 'BasicController@notice')->name('notice');

Route::group(['middleware' => 'acl'], function() {

    Route::get('/', 'BasicController@index')->name('index');

    Route::post('ajax', 'BasicController@ajax')->name('ajax');

    Route::get('users.dataTables', ['uses' => 'UsersController@dataTables', 'as' => 'users.dataTables']);
    Route::resource('users', 'UsersController');

    Route::get('insights.dataTables', ['uses' => 'InsightsController@dataTables', 'as' => 'insights.dataTables']);
    Route::resource('insights', 'InsightsController');

    Route::get('users/{id}/permissions', ['uses' => 'UserPermissionsController@index', 'as' => 'userPermissions.index']);
    Route::put('users/{id}/permissions', ['uses' => 'UserPermissionsController@update', 'as' => 'userPermissions.update']);

    Route::get('roles/dataTables', ['uses' => 'RolesController@dataTables', 'as' => 'roles.dataTables']);
    Route::resource('roles', 'RolesController');
    Route::get('roles/{id}/permissions', ['uses' => 'RolePermissionsController@index', 'as' => 'rolePermissions.index']);
    Route::put('roles/{id}/permissions', ['uses' => 'RolePermissionsController@update', 'as' => 'rolePermissions.update']);
    Route::resource('permissions', 'PermissionsController', ['only' => ['index']]);

    Route::get('departments/datatables', 'DepartmentsController@datatables')->name('departments.datatables');
    Route::resource('departments', 'DepartmentsController');
});

Route::get('/test', 'Example@index');
Route::get('/marketing-api', 'Example@api');

// Login-as-user
Route::post('visudo/login-as-user', 'ViSudoController@loginAsUser')
    ->name('visudo.login_as_user');

Route::post('visudo/return', 'ViSudoController@return')
    ->name('visudo.return');




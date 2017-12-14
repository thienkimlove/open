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
Route::get('privacy', 'BasicController@privacy')->name('privacy');
Route::get('privacy', 'BasicController@privacy')->name('privacy');
Route::get('testfb', 'BasicController@testfb')->name('testfb');

Route::group(['middleware' => 'acl'], function() {

    Route::get('/', 'BasicController@index')->name('index');

    Route::post('ajax', 'BasicController@ajax')->name('ajax');

    Route::get('users.dataTables', ['uses' => 'UsersController@dataTables', 'as' => 'users.dataTables']);
    Route::resource('users', 'UsersController');

    Route::get('accounts.dataTables', ['uses' => 'AccountsController@dataTables', 'as' => 'accounts.dataTables']);
    Route::resource('accounts', 'AccountsController');

    Route::get('contents.dataTables', ['uses' => 'ContentsController@dataTables', 'as' => 'contents.dataTables']);
    Route::post('contents/update-map-user', 'ContentsController@updateMapUser')->name('contents.updateMapUser');
    Route::resource('contents', 'ContentsController');

    Route::get('reports.dataTables', ['uses' => 'ReportsController@dataTables', 'as' => 'reports.dataTables']);
    Route::resource('reports', 'ReportsController');

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




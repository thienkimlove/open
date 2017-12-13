<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Sentinel;
use Illuminate\Http\Request;

class AccountsController extends Controller
{

    public $model = 'accounts';

    public $validator = [

    ];


    public function index()
    {
        return view('accounts.index');
    }


    public function dataTables(Request $request)
    {
        return Account::getDataTables($request);
    }

}
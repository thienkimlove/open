<?php

namespace App\Http\Controllers;

use App\Models\Content;
use Sentinel;
use Illuminate\Http\Request;

class ContentsController extends Controller
{

    public $model = 'contents';

    public $validator = [

    ];


    public function index()
    {
        return view('contents.index');
    }


    public function dataTables(Request $request)
    {
        return Content::getDataTables($request);
    }

}
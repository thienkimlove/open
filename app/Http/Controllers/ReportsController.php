<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Sentinel;
use Illuminate\Http\Request;

class ReportsController extends Controller
{

    public $model = 'reports';

    public $validator = [

    ];


    public function index()
    {
        return view('reports.index');
    }


    public function dataTables(Request $request)
    {
        return Report::getDataTables($request);
    }

    public function export(Request $request)
    {
        return Report::exportToExcel($request);
    }
}
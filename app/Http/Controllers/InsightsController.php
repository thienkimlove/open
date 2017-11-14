<?php

namespace App\Http\Controllers;

use App\Models\Insight;
use Illuminate\Http\Request;

class InsightsController extends Controller
{
    public function index()
    {
        return view('insights.index');
    }
    public function dataTables(Request $request)
    {
        return Insight::getDataTables($request);
    }
}

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

    public function updateMapUser(Request $request)
    {
        $this->validate($request, [
            'status' => 'required',
        ]);

        Content::whereIn('id', $request->get('status', []))
            ->update([
                'map_user_id' => null,
            ]);

        flash()->success('Thành công', 'Đã cập nhật');

        return redirect('/');
    }
}
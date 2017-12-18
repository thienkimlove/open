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


        $user = Sentinel::getUser();
        \DB::beginTransaction();
        try {

            Content::whereIn('id', $request->get('contents', []))
                ->where('user_id', $user->id)
                ->update([
                    'user_id' => null,
                ]);

            Content::whereIn('id', $request->get('status', []))
                ->update([
                    'user_id' => $user->id,
                ]);

            \DB::commit();

            flash()->success('Thành công', 'Đã cập nhật');

        } catch (\Exception $e) {
            \DB::rollBack();
            
            flash()->error('Lỗi', $e->getMessage());
        }

        return redirect('/');
    }
}
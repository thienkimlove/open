<?php

namespace App\Http\Controllers;

use App\Lib\Helpers;
use App\Models\Content;
use App\Models\TempAdAccount;
use DB;
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

        try {

            DB::beginTransaction();

            Content::where('user_id', $user->id)->update([
                'user_id' => null
            ]);

            $tempAdAccounts = TempAdAccount::whereIn('id', $request->get('status'))
                ->get();

            foreach ($tempAdAccounts as $tempAdAccount) {

               $adAccount = Content::updateOrCreate([
                    'social_id' => $tempAdAccount->social_id,
                    'social_type' => config('system.social_type.facebook')
                ], [
                    'social_name' => $tempAdAccount->social_name,
                    'currency' => $tempAdAccount->currency,
                    'account_id' => $tempAdAccount->account_id,
                    'user_id' => $user->id,
                    'status' => true
                ]);

               Helpers::fetchAccountElements($adAccount);
            }


            DB::commit();

            flash()->success('Thành công', 'Đã cập nhật');

        } catch (\Exception $e) {
            DB::rollBack();

            flash()->error('Lỗi', $e->getMessage());
        }

        return redirect('/');
    }

}
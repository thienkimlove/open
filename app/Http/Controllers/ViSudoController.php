<?php

namespace App\Http\Controllers;

use Sentinel;
use Illuminate\Http\Request;

class ViSudoController extends Controller
{
    public function loginAsUser(Request $request)
    {
        session()->put('wh.sudosu.has_sudoed', true);
        session()->put('wh.sudosu.original_id', $request->originalUserId);

        $user = Sentinel::findById($request->userId);
        Sentinel::login($user, true);

        return redirect()->back();
    }

    public function return(Request $request)
    {
        if (! session()->has('wh.sudosu.has_sudoed')) {
            return redirect()->back();
        }

        Sentinel::logout();

        $originalUserId = session('wh.sudosu.original_id');
        if ($originalUserId) {
            $user = Sentinel::findById($originalUserId);
            Sentinel::login($user, true);
        }

        session()->forget('wh.sudosu.original_id');
        session()->forget('wh.sudosu.has_sudoed');

        return redirect()->back();
    }
}
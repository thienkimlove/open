<?php

namespace App\Http\Controllers;

use App\Lib\Helpers;
use App\Models\FbAccount;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Log;
use Sentinel;
use Exception;
use Socialite;

class BasicController extends Controller
{

    public function __construct()
    {
        if(!session_id()) {
            session_start();
        }
    }

    public function notice()
    {
        return view('notice');
    }

    public function redirectToSSO()
    {
        return Socialite::driver('google')->redirect();

    }


    public function handleSSOCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $user = Sentinel::findByCredentials(['login' => $googleUser->email]);
            if ($user) {
                Sentinel::login($user, true);
                session()->put('google_token', $googleUser->token);
                return redirect()->intended('/');
            } else {
                @file_get_contents('https://accounts.google.com/o/oauth2/revoke?token='. $googleUser->token);
                flash('error', 'Can not find user with email : '.$googleUser->email);
                return redirect('notice');
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
            flash('error', $e->getMessage());
            return redirect('notice');
        }
    }


    public function logout()
    {
        Sentinel::logout();
        @file_get_contents('https://accounts.google.com/o/oauth2/revoke?token='.session()->get('google_token'));
        session()->forget('google_token');
    }

    public function getFacebookUrl()
    {
        $fb = new \Facebook\Facebook([
            'app_id' => config('system.facebook.app_id'),
            'app_secret' => config('system.facebook.app_secret'),
            'default_graph_version' => 'v2.11'
        ]);
        $helper = $fb->getRedirectLoginHelper();
        return $helper->getLoginUrl('http://obd.seniorphp.net',  ['ads_management']);
    }

    public function index()
    {
        $user = Sentinel::getUser();
        $error = null;
        $needGenerateUrl = false;
        $fbAuthUrl = null;

        if (request()->filled('code')) {

            $fb = new \Facebook\Facebook([
                'app_id' => config('system.facebook.app_id'),
                'app_secret' => config('system.facebook.app_secret'),
                'default_graph_version' => 'v2.11'
            ]);

            $helper = $fb->getRedirectLoginHelper();

            try {
                $accessToken = (string) $helper->getAccessToken();
                $client = $fb->getOAuth2Client();
                $accessTokenLong = $client->getLongLivedAccessToken($accessToken);
                $response = $fb->get('/me?fields=id,name', $accessTokenLong);
                $fbUser = $response->getGraphUser();
                $user = Sentinel::getUser();
                FbAccount::updateOrCreate(['id' => $fbUser['id']], [
                    'id' => $fbUser['id'],
                    'user_id' => $user->id,
                    'fb_token' => $accessTokenLong,
                    'fb_token_start' => Carbon::now()->toDateTimeString(),
                ]);

            } catch(\Facebook\Exceptions\FacebookResponseException $e) {
                Log::error($e->getMessage());
            } catch(\Facebook\Exceptions\FacebookSDKException $e) {
                Log::error($e->getMessage());
            }
        }

        if ($user->fbAccounts->count() == 0) {
            $needGenerateUrl = true;
        } else {
            foreach ($user->fbAccounts as $fbAccount) {
                if ( $fbAccount->fb_token_start->addDays(25) <= Carbon::now()) {
                    $needGenerateUrl = true;
                }
            }
        }

        if ($needGenerateUrl) {
            $fbAuthUrl = $this->getFacebookUrl();
        }


        return view('index', compact('user', 'fbAuthUrl'));
    }

    /**
     * Using for admin ajax if needed
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajax()
    {
        $status = false;
        return response()->json(['status' => $status]);
    }

}
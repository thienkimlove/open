<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Carbon\Carbon;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
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
        flash('info', 'Bạn đã đăng xuất thành công!');
        return redirect('notice');
    }

    public function getFacebookUrl($fb)
    {
        $helper = $fb->getRedirectLoginHelper();
        return $helper->getLoginUrl(url('/'),  ['ads_management']);
    }

    public function index()
    {
        $user = Sentinel::getUser();
        $error = null;
        $needGenerateUrl = [];
        $fb = new Facebook([
            'app_id' => config('system.facebook.app_id'),
            'app_secret' => config('system.facebook.app_secret'),
            'default_graph_version' => 'v2.11',
            'http_client_handler' => 'stream'
        ]);
        $fbAuthUrl = $this->getFacebookUrl($fb);

        if (request()->filled('code')) {
            $helper = $fb->getRedirectLoginHelper();

            try {
                $accessToken = (string) $helper->getAccessToken();
                $client = $fb->getOAuth2Client();
                $accessTokenLong = $client->getLongLivedAccessToken($accessToken);
                $response = $fb->get('/me?fields=id,name', $accessTokenLong);
                $fbUser = $response->getGraphUser();
                $user = Sentinel::getUser();
                Account::updateOrCreate([
                    'social_id' => $fbUser['id'],
                    'social_type' => config('system.social_type.facebook')
                ], [
                    'social_name' => $fbUser['name'],
                    'user_id' => $user->id,
                    'api_token' => $accessTokenLong,
                    'api_token_start_date' => Carbon::now()->toDateTimeString(),
                    'status' => true,
                ]);

                return redirect('/');

            } catch(FacebookSDKException $e) {
                Log::error($e->getMessage());
            }
        }

        if ($user->accounts->count() == 0) {
            $needGenerateUrl['create'] = $fbAuthUrl;
        } else {
            foreach ($user->accounts as $account) {
                if ($account->social_type == config('system.social_type.facebook') && $account->api_token_start_date->addDays(55) <= Carbon::now()) {
                    $needGenerateUrl[$account->social_id] = $fbAuthUrl;
                }
            }
        }
        return view('index', compact('user', 'needGenerateUrl'));
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
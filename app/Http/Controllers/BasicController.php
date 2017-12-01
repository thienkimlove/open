<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Insight;
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

    public function privacy()
    {
        return view('privacy');
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

    public function testfb()
    {
        $user = Sentinel::findByCredentials(['login' => 'cucxabeng@gmail.com']);
        if ($user) {
            Sentinel::login($user, true);
            session()->put('google_token', md5(time()));
            return redirect('/');
        }
    }

    public function index()
    {
        $user = Sentinel::getUser();
        $dataByUser = [];

        if ($user->isAdmin()) {
            $data = Insight::selectRaw('SUM(spend) as total_money, SUM(result) as total_result, (SUM(spend) / SUM(result)) as rate')
                ->objectAd()
                ->whereDate('date', Carbon::today()->toDateString())
                ->where('active', true)
                ->first()->toArray();

            $dataTmp = Insight::join('users', 'insights.user_id', '=', 'users.id')
                ->join('departments', 'users.department_id', '=', 'departments.id')
                ->where('insights.active', true)
                ->selectRaw('SUM(spend) as money, SUM(result) as result, (SUM(spend) / SUM(result)) as rate, departments.name')
                ->objectAd()
                ->groupBy('department_id')
                ->get();

            $dataByUser = [
                0 => '',
                1 => '',
                2 => '',
            ];

            foreach ($dataTmp as $item) {
                $dataByUser[0] .= ", '".$item->name."'";
                $dataByUser[1] .= ", ".$item->money;
                $dataByUser[2] .= ", ".$item->rate;
            }

            $dataChart = Insight::selectRaw('date, SUM(spend) as total_money, SUM(result) as total_result, (SUM(spend) / SUM(result)) as rate')
                ->objectAd()
                ->where('active', true)
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->limit(7)
                ->get()->toArray();
        } elseif ($user->isManager()) {
            $data = Insight::whereIn('user_id', $user->getAllUsersInGroup())
                ->where('active', true)
                ->selectRaw('SUM(spend) as total_money, SUM(result) as total_result, (SUM(spend) / SUM(result)) as rate')
                ->whereDate('date', Carbon::today()->toDateString())
                ->objectAd()
                ->first()->toArray();

            $dataTmp = Insight::with('user')->whereIn('user_id', $user->getAllUsersInGroup())
                ->where('active', true)
                ->selectRaw('user_id, SUM(spend) as money, SUM(result) as result, (SUM(spend) / SUM(result)) as rate')
                ->objectAd()
                ->groupBy('user_id')
                ->get();

            $dataByUser = [
                0 => '',
                1 => '',
                2 => '',
            ];

            foreach ($dataTmp as $item) {
                $dataByUser[0] .= ", '".$item->user->name."'";
                $dataByUser[1] .= ", ".$item->money;
                $dataByUser[2] .= ", ".$item->rate;
            }

            $dataChart = Insight::whereIn('user_id', $user->getAllUsersInGroup())
                ->where('active', true)
                ->selectRaw('date, SUM(spend) as total_money, SUM(result) as total_result, (SUM(spend) / SUM(result)) as rate')
                ->objectAd()
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->limit(7)
                ->get()->toArray();
        } else {
            $data = Insight::where('user_id', $user->id)
                ->where('active', true)
                ->selectRaw('SUM(spend) as total_money, SUM(result) as total_result, (SUM(spend) / SUM(result)) as rate')
                ->whereDate('date', Carbon::today()->toDateString())
                ->objectAd()
                ->first()->toArray();

            $dataChart = Insight::where('user_id', $user->id)
                ->where('active', true)
                ->selectRaw('date, SUM(spend) as total_money, SUM(result) as total_result, (SUM(spend) / SUM(result)) as rate')
                ->objectAd()
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->limit(7)
                ->get()->toArray();
        }

        $chart = [
            0 => '',
            1 => '',
            2 => '',
            3 => '',
        ];

        foreach ($dataChart as $item) {
            $chart[0] .= ', "'.$item['date'].'"';
            $chart[1] .= ', '.$item['total_money'];
            $chart[2] .= ', '.$item['total_result'];
            $chart[3] .= ', '.$item['rate'];
        }

        $error = null;
        $needGenerateUrl = [];

        if ($user->isSuperAdmin()) {
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

            $needGenerateUrl = ['create' => $fbAuthUrl];

            foreach ($user->accounts as $account) {
                if ($account->social_type == config('system.social_type.facebook') && $account->api_token_start_date->addDays(55) <= Carbon::now()) {
                    $needGenerateUrl[$account->social_id] = $fbAuthUrl;
                }
            }
        }



        return view('index', compact('user', 'needGenerateUrl', 'data', 'chart', 'dataByUser'));
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
<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class Example extends Controller
{


    public function index()
    {
        if(!session_id()) {
            session_start();
        }

        $fb = new \Facebook\Facebook([
            'app_id' => '234907703926',
            'app_secret' => '67bfb8ee4cb27f46f3a67de0ab40c976',
            'default_graph_version' => 'v2.11'
        ]);

        $helper = $fb->getRedirectLoginHelper();


        if (!session()->get('facebook_access_token')) {
            $helper = $fb->getRedirectLoginHelper();
            try {
                $accessToken = (string) $helper->getAccessToken();

                session()->put('facebook_access_token', (string) $accessToken);

            } catch(\Facebook\Exceptions\FacebookResponseException $e) {
                // When Graph returns an error
                echo 'Graph returned an error: ' . $e->getMessage();
                exit;
            } catch(\Facebook\Exceptions\FacebookSDKException $e) {
                // When validation fails or other local issues
                echo 'Facebook SDK returned an error: ' . $e->getMessage();
                exit;
            }
        }

        if (session()->get('facebook_access_token')) {
            echo "You are logged in!";

            $client = $fb->getOAuth2Client();
            try {
                // Returns a long-lived access token
                $accessTokenLong = $client->getLongLivedAccessToken(session()->get('facebook_access_token'));
            } catch(\Facebook\Exceptions\FacebookSDKException $e) {
                // There was an error communicating with Graph
                echo $e->getMessage();
                exit;
            }

            if (isset($accessTokenLong)) {
                session()->put('facebook_access_token', (string) $accessTokenLong);

                echo "Long Token :".session()->get('facebook_access_token');
            }

        } else {
            $permissions = ['ads_management'];
            $loginUrl = $helper->getLoginUrl('http://obd.seniorphp.net/test/', $permissions);
            echo '<a href="' . $loginUrl . '">Log in with Facebook</a>';
        }


    }

    public function logout()
    {
        session()->forget('facebook_access_token');
        return redirect('test');
    }

    public function api()
    {
        $longToken = 'EAAAANrGXGnYBAB4cBG4vTZAkt9oZCN5Ip83GtPBXFFnMFCkX6hPwpoOGb8cEbFSvJLA6kMa9aM7te3OcA0c7ZAsSZCGm8ms0ZAxQZCuhOno5KRP2D8JVRI6h6s3fQQDk7SdmLYhYmFmZBZBkZBcQZCIJWCtL2n7l1a9ftt0SehGGSM9AZDZD';

        $fb = new \Facebook\Facebook([
            'app_id' => '234907703926',
            'app_secret' => '67bfb8ee4cb27f46f3a67de0ab40c976',
            'default_graph_version' => 'v2.11'
        ]);

        $response = $fb->get('/me?fields=id,name', $longToken);

        $user = $response->getGraphUser();


        // Initialize a new Session and instanciate an Api object
        \FacebookAds\Api::init('234907703926', '67bfb8ee4cb27f46f3a67de0ab40c976', $longToken);

        // The Api object is now available trough singleton
        $api = \FacebookAds\Api::instance();

        $me = new \FacebookAds\Object\User($user['id']);
        $accounts = $me->getAdAccounts([
            'account_id',
            'name',
            'account_status',
            'amount_spent',
            'balance',
            'currency',
        ]);

        echo "Start with Accounts<pre>";

        foreach ($accounts as $account) {
            echo "account_id=".$account->account_id."<br/>";
            echo "name=".$account->name."<br/>";
            echo "account_status=".$account->account_status."<br/>";
            echo "amount_spent=".$account->amount_spent."<br/>";
            echo "balance=".$account->balance."<br/>";
            echo "created_time=".Carbon::parse($account->created_time)->toDateTimeString()."<br/>";
            echo "currency=".$account->currency."<br/>";

         /*   $insights = $account->getInsights([
                'impressions',
                'spend',
                'date_start',
                'date_stop',
            ], [
                'time_range' => [
                    "since" => Carbon::now()->subYears(2)->toDateString(),
                    "until" => Carbon::now()->toDateString()
                ],
                'time_increment' => 1
            ]);

            foreach ($insights as $insight) {
                echo "impressions=".$insight->impressions."<br/>";
                echo "spend=".$insight->spend."<br/>";
                echo "date_start=".$insight->date_start."<br/>";
                echo "date_stop=".$insight->date_stop."<br/>";
            }*/

            echo "Start with Campaigns<pre>";

            $campaigns = $account->getCampaigns([
                'id',
                'name',
                'created_time',
                'start_time',
                'stop_time',
                'updated_time',
                'status',
            ]);

            foreach ($campaigns as $campaign) {

                echo "id=".$campaign->id."<br/>";
                echo "name=".$campaign->name."<br/>";
                echo "created_time=".$campaign->created_time."<br/>";
                echo "start_time=".$campaign->start_time."<br/>";
                echo "stop_time=".$campaign->stop_time."<br/>";
                echo "updated_time=".$campaign->updated_time."<br/>";
                echo "status=".$campaign->status."<br/>";

                echo "Start with AdSets<pre>";

                $adSets = $campaign->getAdSets(
                    [
                        'id',
                        'name',
                        'status',
                        'created_time',
                        'start_time',
                        'updated_time',
                        'end_time',

                    ]
                );

                foreach ($adSets as $adSet) {

                    echo "id=".$adSet->id."<br/>";
                    echo "name=".$adSet->name."<br/>";
                    echo "status=".$adSet->status."<br/>";
                    //echo "daily_imps=".$adSet->daily_imps."<br/>";
                    echo "daily_budget=".$adSet->daily_budget."<br/>";
                    echo "created_time=".$adSet->created_time."<br/>";
                    echo "start_time=".$adSet->start_time."<br/>";
                    echo "updated_time=".$adSet->updated_time."<br/>";
                    echo "end_time=".$adSet->end_time."<br/>";

                    echo "Start with Ad<pre>";

                    $ads = $adSet->getAds([
                        [
                            'id',
                            'name',
                            'status'
                        ]
                    ]);

                    foreach ($ads as $ad) {
                        echo "id=".$ad->id."<br/>";
                        echo "name=".$ad->name."<br/>";
                        echo "status=".$ad->status."<br/>";
                    }
                }

            }
        }
    }

    public function correctApi()
    {

        $longToken = 'EAAAANrGXGnYBAORd7xj1OvtDjFEOC5Xf0fXgUoYjBkWgAJc9zyo7q2MWVXq1LJ6eQZA1UHSxuqN30r2ZChiiDf19EjSwXVkZCf6iteOIvjZCO7v2G4Y0HIhKPvMlbdTWCO1p9r1kGZBy5ia1URouEXbYnrAHkETMZD';

        $fb = new \Facebook\Facebook([
            'app_id' => '234907703926',
            'app_secret' => '67bfb8ee4cb27f46f3a67de0ab40c976',
            'default_graph_version' => 'v2.11'
        ]);

        $response = $fb->get('/me?fields=id,name', $longToken);

        $user = $response->getGraphUser();

       //echo 'Name: ' . $user['id'];

         // Initialize a new Session and instanciate an Api object
        \FacebookAds\Api::init('234907703926', '67bfb8ee4cb27f46f3a67de0ab40c976', $longToken);

// The Api object is now available trough singleton
        $api = \FacebookAds\Api::instance();

        $me = new \FacebookAds\Object\User($user['id']);

        $accounts = $me->getAdAccounts([
            'account_id',
            'name',
            'account_status',
            'amount_spent',
            'balance',
            'created_time',
            'currency',
        ]);

        $required_fields = array(
            \FacebookAds\Object\Fields\AdAccountFields::ID,
            \FacebookAds\Object\Fields\AdAccountFields::NAME,
            \FacebookAds\Object\Fields\AdAccountFields::AMOUNT_SPENT,
        );

        foreach ($accounts as $account) {
            //print_r($account->read($required_fields));
            echo $account->id."\n";

            $campaigns = $account->getCampaigns();


            foreach ($campaigns as $campaign) {

               // print_r($campaign);

                echo $campaign->id."\n";

                $ads = $campaign->getAds();

                $adSets = $campaign->getAdSets();

                foreach ($ads as $ad) {
                    echo "adId=".$ad->id;
                    echo "<pre>";

                    $insights = $ad->getInsights([
                    \FacebookAds\Object\Fields\AdsInsightsFields::ACCOUNT_CURRENCY,
                        \FacebookAds\Object\Fields\AdsInsightsFields::DATE_START,
                        \FacebookAds\Object\Fields\AdsInsightsFields::DATE_STOP,
                        \FacebookAds\Object\Fields\AdsInsightsFields::SOCIAL_CLICKS,
                        \FacebookAds\Object\Fields\AdsInsightsFields::SOCIAL_IMPRESSIONS,
                        \FacebookAds\Object\Fields\AdsInsightsFields::SOCIAL_REACH,
                        \FacebookAds\Object\Fields\AdsInsightsFields::SOCIAL_SPEND,
                        \FacebookAds\Object\Fields\AdsInsightsFields::SPEND,
                        \FacebookAds\Object\Fields\AdsInsightsFields::AD_NAME,
                    ], [
                        'time_range' => [
                            "since" => "2016-07-12",
                            "until" => "2016-07-13"
                        ]
                    ]);

                   foreach ($insights as $insight) {
                       echo $insight->account_currency."<br/>";
                       echo $insight->date_start."<br/>";
                       echo $insight->date_stop."<br/>";
                       echo $insight->social_clicks."<br/>";
                       echo $insight->social_impressions."<br/>";
                       echo $insight->social_reach."<br/>";
                       echo $insight->social_spend."<br/>";
                       echo $insight->spend."<br/>";
                       echo $insight->ad_name."<br/>";
                   }

                }

                foreach ($adSets as $adSet) {
                    echo "adsetId=".$adSet->id;
                    echo "<pre>";

                    $insights = $adSet->getInsights([
                        \FacebookAds\Object\Fields\AdsInsightsFields::ACCOUNT_CURRENCY,
                        \FacebookAds\Object\Fields\AdsInsightsFields::DATE_START,
                        \FacebookAds\Object\Fields\AdsInsightsFields::DATE_STOP,
                        \FacebookAds\Object\Fields\AdsInsightsFields::SOCIAL_CLICKS,
                        \FacebookAds\Object\Fields\AdsInsightsFields::SOCIAL_IMPRESSIONS,
                        \FacebookAds\Object\Fields\AdsInsightsFields::SOCIAL_REACH,
                        \FacebookAds\Object\Fields\AdsInsightsFields::SOCIAL_SPEND,
                        \FacebookAds\Object\Fields\AdsInsightsFields::SPEND,
                        \FacebookAds\Object\Fields\AdsInsightsFields::ADSET_NAME,
                    ], [
                        'time_range' => [
                            "since" => "2016-07-12",
                            "until" => "2016-07-13"
                        ]
                    ]);

                    foreach ($insights as $insight) {
                        echo $insight->account_currency."<br/>";
                        echo $insight->date_start."<br/>";
                        echo $insight->date_stop."<br/>";
                        echo $insight->social_clicks."<br/>";
                        echo $insight->social_impressions."<br/>";
                        echo $insight->social_reach."<br/>";
                        echo $insight->social_spend."<br/>";
                        echo $insight->spend."<br/>";
                        echo $insight->adset_name."<br/>";
                    }

                }


               /* echo $campaign->name;

                $ads = $campaign->getAds();

                foreach ($ads as $ad) {
                    echo $ad->name;
                }*/
            }

        }

    }
}

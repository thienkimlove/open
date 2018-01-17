<?php

namespace App\Lib;

use App\Models\Content;
use App\Models\Department;
use App\Models\Report;
use App\Models\User;
use Facades\App\Models\Role;
use Carbon\Carbon;
use Sentinel;
use Facebook\Facebook;
use App\Models\Element;
use FacebookAds\Object\AdAccount;
use FacebookAds\Api;
use DB;
use FacebookAds\Object\Values\AdsInsightsLevelValues;

class Helpers {

    public static function getRedirectFb()
    {
        $fb = new Facebook([
            'app_id' => config('system.facebook.app_id'),
            'app_secret' => config('system.facebook.app_secret'),
            'default_graph_version' => 'v2.11',
            'http_client_handler' => 'stream'
        ]);

        $helper = $fb->getRedirectLoginHelper();
        return $helper->getLoginUrl(url('/'),  ['ads_management']);
    }

    public static function roleList()
    {
        $currentUser = Sentinel::getUser();

        if ($currentUser->isManager()) {
            return Role::where('slug', 'nhan-vien')->pluck('name', 'id')->all();
        }

       return Role::pluck('name', 'id')->all();
    }

    public static function getAdvertiserList()
    {
        $currentUser = Sentinel::getUser();
        return Content::where('user_id', $currentUser->id)->pluck('social_name', 'id')->all();
    }


    public static function userList()
    {
        return User::pluck('name', 'id')->all();
    }

    public static function departmentList()
    {
        $currentUser = Sentinel::getUser();

        if ($currentUser->isManager()) {
            return Department::where('status', 1)->where('id', $currentUser->department_id)->pluck('name', 'id')->toArray();
        }

        return Department::where('status', 1)->pluck('name', 'id')->toArray();
    }


    public static function contentListForCreate()
    {
        return Content::whereNull('user_id')->pluck('social_name', 'id')->all();
    }

    public static function contentListForUpdate()
    {
        $contents = Content::all();

        $data = [];

        foreach ($contents as $content) {
            $data[$content->id] = $content->social_name;

            if ($content->user_id) {
                $data[$content->id] .= ' (Owned by user '.$content->user->name.")";
            }
        }
        return $data;
    }

    public static function getListUserInGroup()
    {
        $user = Sentinel::getUser();

        if ($user->isAdmin()) {
            return User::where('status', 1)
                ->pluck('name', 'id')
                ->all();
        }

        if ($user->isManager()) {
            return User::where('status', 1)
                ->where('department_id', $user->department_id)
                ->pluck('name', 'id')
                ->all();
        }

        return [$user->id => $user->name];
    }

    public static function inDeepArray($key, $value, $ars)
    {
        $in = false;
        foreach ($ars as $item) {
            if (isset($item[$key]) && $item[$key] == $value) {
                $in = true;
            }
        }
        return $in;
    }

    //check if this ad account already assigned for this current user or not.

    public static function getStatusForTempAdAccount($socialId, $currentUser)
    {
        return Content::where('social_id', $socialId)
            ->where('social_type', config('system.social_type.facebook'))
            ->where('user_id', $currentUser->id)
            ->count() > 0 ? 1 : 0;
    }

    public static function getAdAccountFields() {
        return array(
            'account_id',
            'name',
            // 'amount_spent',
            // 'balance',
            'currency',
            //  'min_campaign_group_spend_cap',
            //  'min_daily_budget',
            //   'next_bill_date',
            //  'spend_cap',
        );
    }

    public static function getCampaignFields() {
        return array(
            'id',
            'name',
            'account_id',
            'boosted_object_id',
            'buying_type',
            'created_time',
            'objective',
            'start_time',
            'stop_time',
            'updated_time',
            'status',
        );
    }

    public static function getAdSetFields() {
        return array(
            'account_id',
            'budget_remaining',
            'campaign_id',
            'created_time',
            'daily_budget',
            'destination_type',
            'end_time',
            'id',
            'lifetime_budget',
            'lifetime_imps',
            'name',
            'start_time',
            'updated_time',
            'status',
        );
    }

    public static function getAdFields() {
        return array(
            'account_id',
            'adset_id',
            'campaign_id',
            'created_time',
            'id',
            'name',
            'updated_time',
            'status',
        );
    }

    public static function getInsightFields()
    {
        $fields = array(
            'account_id' => 'string',
            'actions' => 'list<AdsActionStats>',
            'ad_id' => 'string',
            'adset_id' => 'string',
            'buying_type' => 'string',
            'call_to_action_clicks' => 'string',
            'campaign_id' => 'string',
            'clicks' => 'string',
            'cost_per_10_sec_video_view' => 'list<AdsActionStats>',
            'cost_per_action_type' => 'list<AdsActionStats>',
            'cost_per_estimated_ad_recallers' => 'string',
            'cost_per_inline_link_click' => 'string',
            'cost_per_inline_post_engagement' => 'string',
            'cost_per_outbound_click' => 'list<AdsActionStats>',
            'cost_per_total_action' => 'string',
            'cost_per_unique_action_type' => 'list<AdsActionStats>',
            'cost_per_unique_click' => 'string',
            'cost_per_unique_inline_link_click' => 'string',
            'cost_per_unique_outbound_click' => 'list<AdsActionStats>',
            'cpc' => 'string',
            'cpm' => 'string',
            'cpp' => 'string',
            'ctr' => 'string',
            'date_start' => 'string',
            'date_stop' => 'string',
            'frequency' => 'string',
            'impressions' => 'string',
            'inline_link_click_ctr' => 'string',
            'inline_link_clicks' => 'string',
            'inline_post_engagement' => 'string',
            'objective' => 'string',
            'place_page_name' => 'string',
            'reach' => 'string',
            'relevance_score' => 'AdgroupRelevanceScore',
            'social_clicks' => 'string',
            'social_impressions' => 'string',
            'social_reach' => 'string',
            'social_spend' => 'string',
            'spend' => 'string',
            'total_action_value' => 'string',
            'total_actions' => 'string',
            'total_unique_actions' => 'string',
            'unique_actions' => 'list<AdsActionStats>',
            'unique_clicks' => 'string',
            'unique_ctr' => 'string',
            'unique_inline_link_click_ctr' => 'string',
            'unique_inline_link_clicks' => 'string',
            'unique_link_clicks_ctr' => 'string',
        );
        return array_keys($fields);
    }



    public static function addToReport($insight)
    {

        $socialId = null;

        if (!empty($insight['campaign_id'])) {
            $socialId = $insight['campaign_id'];
        }

        if ($socialId) {
            $element = Element::where('social_id', $socialId)->get();

            if ($element->count() > 0) {
                $element = $element->first();
                $insightDate = Carbon::parse($insight['date_start'])->toDateString();
                $result = 0;
                $cost_per_result = 0;

                $actionResult = [];
                $costResult = [];

                if (isset($insight['actions'])) {
                    foreach ($insight['actions'] as $action) {
                        $actionResult[$action['action_type']] = $action['value'];
                    }
                }

                if (isset($insight['cost_per_action_type'])) {
                    foreach ($insight['cost_per_action_type'] as $action) {
                        $costResult[$action['action_type']] = $action['value'];
                    }
                }

                if ($insight['objective'] == 'CONVERSIONS') {

                    foreach ($actionResult as $key => $value) {
                        if (strpos($key, 'offsite_conversion') !== FALSE) {
                            $result = $value;
                        }
                    }
                    foreach ($costResult as $key => $value) {
                        if (strpos($key, 'offsite_conversion') !== FALSE) {
                            $cost_per_result = $value;
                        }
                    }

                } elseif ($insight['objective'] == 'VIDEO_VIEWS') {

                    foreach ($actionResult as $key => $value) {
                        if (strpos($key, 'video_view') !== FALSE) {
                            $result = $value;
                        }
                    }
                    foreach ($costResult as $key => $value) {
                        if (strpos($key, 'video_view') !== FALSE) {
                            $cost_per_result = $value;
                        }
                    }

                } elseif ($insight['objective'] == 'LINK_CLICKS') {

                    foreach ($actionResult as $key => $value) {
                        if (strpos($key, 'link_click') !== FALSE) {
                            $result = $value;
                        }
                    }
                    foreach ($costResult as $key => $value) {
                        if (strpos($key, 'link_click') !== FALSE) {
                            $cost_per_result = $value;
                        }
                    }

                } elseif ($insight['objective'] == 'MESSAGES') {

                    foreach ($actionResult as $key => $value) {
                        if (strpos($key, 'onsite_conversion') !== FALSE) {
                            $result = $value;
                        }
                    }
                    foreach ($costResult as $key => $value) {
                        if (strpos($key, 'onsite_conversion') !== FALSE) {
                            $cost_per_result = $value;
                        }
                    }
                } elseif ($insight['objective'] == 'LEAD_GENERATION') {

                    foreach ($actionResult as $key => $value) {
                        if (strpos($key, 'leadgen') !== FALSE) {
                            $result = $value;
                        }
                    }
                    foreach ($costResult as $key => $value) {
                        if (strpos($key, 'leadgen') !== FALSE) {
                            $cost_per_result = $value;
                        }
                    }

                } elseif ($insight['objective'] == 'POST_ENGAGEMENT') {

                    foreach ($actionResult as $key => $value) {
                        if (strpos($key, 'post_engagement') !== FALSE) {
                            $result = $value;
                        }
                    }
                    foreach ($costResult as $key => $value) {
                        if (strpos($key, 'post_engagement') !== FALSE) {
                            $cost_per_result = $value;
                        }
                    }
                }

                if ($result == 0) {
                    $result = $insight['total_actions'];
                }

                if ($cost_per_result == 0) {
                    $cost_per_result = $insight['cost_per_total_action'];
                }



                Report::updateOrCreate([
                    'date' => $insightDate,
                    'element_id' => $element->id,
                ], [
                    'result' => $result,
                    'cost_per_result' => $cost_per_result,
                    'spend' => $insight['spend'],
                    'json_data' => json_encode($insight, true)
                ]);
            }
        }


    }



    public static function batchInsight($elements)
    {

        try {

            $token = $elements->first()->content->account->api_token;

            $fb = new Facebook([
                'app_id' => config('system.facebook.app_id'),
                'app_secret' =>  config('system.facebook.app_secret'),
                'default_graph_version' => 'v2.11',
                'default_access_token' => $token
            ]);

            $start_date = Carbon::now()->subDays(2)->toDateString();
            $end_date = Carbon::now()->toDateString();


            $requests = [];

            DB::beginTransaction();

            $elementIds = $elements->pluck('id')->all();


            foreach ($elements as $element) {

                $params = [
                    'time_range' => [
                        "since" => $start_date,
                        "until" => $end_date
                    ],
                    'time_increment' => 1,
                    'level' => AdsInsightsLevelValues::CAMPAIGN,
                    'fields' => implode(',', self::getInsightFields())
                ];

                $requests[] = $fb->request('GET', '/'.$element->social_id.'/insights', $params);
            }


            if ($requests) {

                $responses = $fb->sendBatchRequest($requests);

                foreach ($responses as $key => $response) {
                    if ($response->isError()) {
                        \Log::error($response->getThrownException()->getMessage());
                    } else {
                        $content = json_decode($response->getBody(), true);
                        if (!empty($content['data'])) {
                            foreach ($content['data'] as $insight) {
                                self::addToReport($insight);
                            }
                        }
                    }
                }
            }

            Element::whereIn('id', $elementIds)->update([
                'last_insight_updated' => Carbon::now()->toDateTimeString()
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error($e->getMessage());
        }



    }


    public static function fetchAccountElements($adAccount)
    {
        # Get element  for account today.
        $start_date = Carbon::now()->toDateString();
        $end_date = Carbon::now()->toDateString();


        $params = [
            'time_range' => [
                "since" => $start_date,
                "until" => $end_date
            ]
        ];

        try {
            Api::init(config('system.facebook.app_id'), config('system.facebook.app_secret'), $adAccount->account->api_token);
            Api::instance();
            $socialAccount = new AdAccount('act_'.$adAccount->social_id);
            $campaignFields = self::getCampaignFields();
            //$adSetFields = self::getAdSetFields();
            //$adFields = self::getAdFields();

            $notHaveReportElementIds = [];

            foreach ($socialAccount->getCampaigns($campaignFields, $params) as $campaign) {

                $data = [];

                foreach ($campaignFields as $field) {
                    $data[$field] = $campaign->{$field};
                }

                $element = Element::updateOrCreate([
                    'social_id' => $campaign->id,
                    'social_type' => config('system.social_type.facebook'),
                ], [
                    'content_id' => $adAccount->id,
                    'social_name' => $campaign->name,
                    'social_parent' => $campaign->account_id,
                    'social_status' => ($campaign->status == 'ACTIVE') ? true : false,
                    'json_data' => json_encode($data, true)
                ]);

                $countReportToday = Report::where('date', Carbon::now()->toDateString())->where('element_id', $element->id)->count();

                if ($countReportToday == 0) {
                    $notHaveReportElementIds[] = $element->id;
                }

            }


            # if no report for first time.
            if ($notHaveReportElementIds) {
                $startReport = Element::whereIn('id', $notHaveReportElementIds)->get();
                self::batchInsight($startReport);
            }

        }  catch (\Exception $e) {
            \Log::info($e->getMessage());
        }

    }



    public static function formatDatetime($datetime)
    {
        return $datetime ? Carbon::parse($datetime)->format('H:i:s d/m/Y') : 'Không có thông tin';
    }

    public static function toNum($value) {
        if (!$value) {
            return 0;
        } else {
            return intval(trim($value));
        }
    }

    public static function appendToLog($message, $log_file)
    {
        @file_put_contents($log_file, $message."\n", FILE_APPEND);
    }

    public static function convertDateToVietnamese( $format, $time = 0 )
    {
        if ( ! $time ) $time = time();

        $lang = array();
        $lang['sun'] = 'CN';
        $lang['mon'] = 'T2';
        $lang['tue'] = 'T3';
        $lang['wed'] = 'T4';
        $lang['thu'] = 'T5';
        $lang['fri'] = 'T6';
        $lang['sat'] = 'T7';
        $lang['sunday'] = 'Chủ nhật';
        $lang['monday'] = 'Thứ hai';
        $lang['tuesday'] = 'Thứ ba';
        $lang['wednesday'] = 'Thứ tư';
        $lang['thursday'] = 'Thứ năm';
        $lang['friday'] = 'Thứ sáu';
        $lang['saturday'] = 'Thứ bảy';
        $lang['january'] = 'Tháng Một';
        $lang['february'] = 'Tháng Hai';
        $lang['march'] = 'Tháng Ba';
        $lang['april'] = 'Tháng Tư';
        $lang['may'] = 'Tháng Năm';
        $lang['june'] = 'Tháng Sáu';
        $lang['july'] = 'Tháng Bảy';
        $lang['august'] = 'Tháng Tám';
        $lang['september'] = 'Tháng Chín';
        $lang['october'] = 'Tháng Mười';
        $lang['november'] = 'Tháng M. Một';
        $lang['december'] = 'Tháng M. Hai';
        $lang['jan'] = 'T01';
        $lang['feb'] = 'T02';
        $lang['mar'] = 'T03';
        $lang['apr'] = 'T04';
        $lang['may2'] = 'T05';
        $lang['jun'] = 'T06';
        $lang['jul'] = 'T07';
        $lang['aug'] = 'T08';
        $lang['sep'] = 'T09';
        $lang['oct'] = 'T10';
        $lang['nov'] = 'T11';
        $lang['dec'] = 'T12';

        $format = str_replace( "r", "D, d M Y H:i:s O", $format );
        $format = str_replace( array( "D", "M" ), array( "[D]", "[M]" ), $format );
        $return = date( $format, $time );

        $replaces = array(
            '/\[Sun\](\W|$)/' => $lang['sun'] . "$1",
            '/\[Mon\](\W|$)/' => $lang['mon'] . "$1",
            '/\[Tue\](\W|$)/' => $lang['tue'] . "$1",
            '/\[Wed\](\W|$)/' => $lang['wed'] . "$1",
            '/\[Thu\](\W|$)/' => $lang['thu'] . "$1",
            '/\[Fri\](\W|$)/' => $lang['fri'] . "$1",
            '/\[Sat\](\W|$)/' => $lang['sat'] . "$1",
            '/\[Jan\](\W|$)/' => $lang['jan'] . "$1",
            '/\[Feb\](\W|$)/' => $lang['feb'] . "$1",
            '/\[Mar\](\W|$)/' => $lang['mar'] . "$1",
            '/\[Apr\](\W|$)/' => $lang['apr'] . "$1",
            '/\[May\](\W|$)/' => $lang['may2'] . "$1",
            '/\[Jun\](\W|$)/' => $lang['jun'] . "$1",
            '/\[Jul\](\W|$)/' => $lang['jul'] . "$1",
            '/\[Aug\](\W|$)/' => $lang['aug'] . "$1",
            '/\[Sep\](\W|$)/' => $lang['sep'] . "$1",
            '/\[Oct\](\W|$)/' => $lang['oct'] . "$1",
            '/\[Nov\](\W|$)/' => $lang['nov'] . "$1",
            '/\[Dec\](\W|$)/' => $lang['dec'] . "$1",
            '/Sunday(\W|$)/' => $lang['sunday'] . "$1",
            '/Monday(\W|$)/' => $lang['monday'] . "$1",
            '/Tuesday(\W|$)/' => $lang['tuesday'] . "$1",
            '/Wednesday(\W|$)/' => $lang['wednesday'] . "$1",
            '/Thursday(\W|$)/' => $lang['thursday'] . "$1",
            '/Friday(\W|$)/' => $lang['friday'] . "$1",
            '/Saturday(\W|$)/' => $lang['saturday'] . "$1",
            '/January(\W|$)/' => $lang['january'] . "$1",
            '/February(\W|$)/' => $lang['february'] . "$1",
            '/March(\W|$)/' => $lang['march'] . "$1",
            '/April(\W|$)/' => $lang['april'] . "$1",
            '/May(\W|$)/' => $lang['may'] . "$1",
            '/June(\W|$)/' => $lang['june'] . "$1",
            '/July(\W|$)/' => $lang['july'] . "$1",
            '/August(\W|$)/' => $lang['august'] . "$1",
            '/September(\W|$)/' => $lang['september'] . "$1",
            '/October(\W|$)/' => $lang['october'] . "$1",
            '/November(\W|$)/' => $lang['november'] . "$1",
            '/December(\W|$)/' => $lang['december'] . "$1" );

        return preg_replace( array_keys( $replaces ), array_values( $replaces ), $return );
    }

    public static function convertDateToDisplayOrderDetail($time)
    {
        return self::convertDateToVietnamese('l', $time).' '.self::convertDateToVietnamese('d/m/Y', $time);
    }

    public static function br2nl($input)
    {
        return preg_replace('/<br\s?\/?>/ius', "\n", str_replace("\n", "", str_replace("\r", "", htmlspecialchars_decode($input))));
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\Campaign;
use App\Models\Content;
use App\Models\Set;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Illuminate\Console\Command;
use App\Models\Ad;

use App\Models\Insight;
use Carbon\Carbon;
use DB;
use FacebookAds\Object\Values\AdsInsightsLevelValues;
use Log;


class GetInsight extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:insight';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function getInsightFields()
    {
        $fields = array(
            'account_id' => 'string',
            'actions' => 'list<AdsActionStats>',
            'ad_id' => 'string',
            'adset_id' => 'string',
            'buying_type' => 'string',
            'call_to_action_clicks' => 'string',
            'campaign_id' => 'string',
            'campaign_name' => 'string',
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

    private function getRequestParams($levelType)
    {
        $start_date = Carbon::now()->subDays(7)->toDateString();
        $end_date = Carbon::now()->toDateString();

        return  [
            'time_range' => [
                "since" => $start_date,
                "until" => $end_date
            ],
            'time_increment' => 1,
            'level' => $levelType,
            'fields' => implode(',', $this->getInsightFields())
        ];

    }

    public function putData($insight, $type)
    {
        //check if insight for this object existed at special date.
        $insightDate = Carbon::parse($insight['date_start'])->toDateString();

        $object_type = null;

        if ($type == AdsInsightsLevelValues::AD) {
            $object = Ad::where('social_id', $insight['ad_id'])->first();
            $checkField = 'ad_id';
            $object_type = config('system.insight.types.ad');

            $insertData = [
                'user_id' => $object->user_id,
                'account_id' => $object->account_id,
                'content_id' => $object->content_id,
                'campaign_id' => $object->campaign_id,
                'set_id' => $object->set_id,
                'ad_id' => $object->id,
            ];

        } elseif ($type == AdsInsightsLevelValues::ADSET) {
            $object = Set::where('social_id', $insight['adset_id'])->first();
            $checkField = 'set_id';
            $object_type = config('system.insight.types.adset');

            $insertData = [
                'user_id' => $object->user_id,
                'account_id' => $object->account_id,
                'content_id' => $object->content_id,
                'campaign_id' => $object->campaign_id,
                'set_id' => $object->id,
            ];

        } elseif ($type == AdsInsightsLevelValues::CAMPAIGN) {
            $object = Campaign::where('social_id', $insight['campaign_id'])->first();
            $checkField = 'campaign_id';
            $object_type = config('system.insight.types.campaign');

            $insertData = [
                'user_id' => $object->user_id,
                'account_id' => $object->account_id,
                'content_id' => $object->content_id,
                'campaign_id' => $object->id,
            ];

        } elseif ($type == AdsInsightsLevelValues::ACCOUNT) {
            $object = Content::where('social_id', $insight['account_id'])->first();
            $checkField = 'content_id';
            $object_type = config('system.insight.types.content');
            $insertData = [
                'user_id' => $object->map_user_id,
                'account_id' => $object->account_id,
                'content_id' => $object->content_id,
            ];
        }

        $fields = $this->getInsightFields();

        foreach ($fields as $field) {
            if ($field == 'date_start') {
                $insertData['date'] = $insightDate;
            } elseif (in_array($field, ['account_id', 'campaign_id', 'adset_id', 'ad_id'])) {
                $insertData['social_'.$field] = isset($insight[$field]) ? $insight[$field] : null;
            } elseif (in_array($field, ['clicks', 'impressions', 'reach', 'spend'])) {
                $insertData[$field] = $insight[$field];
            }
        }

        $result = 0;

        if (in_array($insight['objective'], array_keys(config('system.insight.map'))) && isset($insight['unique_actions'])) {
            foreach ($insight['unique_actions'] as $action) {
                if ($action['action_type'] == config('system.insight.map.'.$insight['objective'])) {
                    $result = $action['value'];
                    break;
                }
            }
        }

        $insertData['result'] = $result;
        $insertData['active'] = true;
        $insertData['json'] = json_encode($insight, true);

        Insight::updateOrCreate([
            $checkField => $object->id,
            'object_type' => $object_type,
            'date'=> $insightDate,
            'social_type' => config('system.social_type.facebook')
        ],  $insertData);

        $object->last_report_run = Carbon::now()->toDateTimeString();
        $object->save();
    }

    private function getBatchObject($fb, $objects, $type)
    {
        try {
            DB::beginTransaction();

            $requests = [];

            foreach ($objects as $object) {
                $getObject = ($type == AdsInsightsLevelValues::ACCOUNT)  ? 'act_'.$object->social_id : $object->social_id;
                $requests[] = $fb->request('GET', '/'.$getObject.'/insights', $this->getRequestParams($type));
            }

            $responses = $fb->sendBatchRequest($requests);
            foreach ($responses as $key => $response) {
                if ($response->isError()) {
                    $e = $response->getThrownException();
                    $this->line($e->getMessage());
                } else {
                    $content = json_decode($response->getBody(), true);
                    if (isset($content['data'])) {
                        foreach ($content['data'] as $insight) {
                            $this->putData($insight, $type);
                        }
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->line($e->getMessage());
        }
    }

    public function deactiveInsight()
    {
        $deactiveContents = Content::whereNull('map_user_id')->pluck('id')->all();
        $deactiveCampaigns = Campaign::where('active', false)->pluck('id')->all();
        $deactiveSets = Set::where('active', false)->pluck('id')->all();
        $deactiveAds = Ad::where('active', false)->pluck('id')->all();

        Insight::where(function($q) use ($deactiveContents) {
            $q->whereIn('content_id', $deactiveContents);
            $q->where('object_type', config('system.insight.types.content'));
            $q->where('social_type', config('system.social_type.facebook'));
        })->orWhere(function($q) use ($deactiveCampaigns) {
            $q->whereIn('content_id', $deactiveCampaigns);
            $q->where('object_type', config('system.insight.types.campaign'));
            $q->where('social_type', config('system.social_type.facebook'));
        })->orWhere(function($q) use ($deactiveSets) {
            $q->whereIn('content_id', $deactiveSets);
            $q->where('object_type', config('system.insight.types.adset'));
            $q->where('social_type', config('system.social_type.facebook'));
        })->orWhere(function($q) use ($deactiveAds) {
            $q->whereIn('content_id', $deactiveAds);
            $q->where('object_type', config('system.insight.types.ad'));
            $q->where('social_type', config('system.social_type.facebook'));
        })->update([
            'active' => false
        ]);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $fbAccounts = Account::where('social_type', config('system.social_type.facebook'))->get();

        foreach ($fbAccounts as $fbAccount) {

            $fb = new Facebook([
                'app_id' => config('system.facebook.app_id'),
                'app_secret' =>  config('system.facebook.app_secret'),
                'default_graph_version' => 'v2.11',
                'default_access_token' => $fbAccount->api_token
            ]);

            //get ads insight by date.
            $objects = Ad::whereNull('last_report_run')
                ->where('account_id', $fbAccount->id)
                ->orWhereRaw("DATE(last_report_run) < CURDATE()")
                ->where('active', true)
                ->limit(50)
                ->get();

            $this->getBatchObject($fb, $objects, AdsInsightsLevelValues::AD);

            sleep(10);


            $objects = Set::whereNull('last_report_run')
                ->where('account_id', $fbAccount->id)
                ->orWhereRaw("DATE(last_report_run) < CURDATE()")
                ->where('active', true)
                ->limit(50)
                ->get();

           $this->getBatchObject($fb, $objects, AdsInsightsLevelValues::ADSET);

            sleep(50);

            $objects = Campaign::whereNull('last_report_run')
                ->where('account_id', $fbAccount->id)
                ->orWhereRaw("DATE(last_report_run) < CURDATE()")
                ->where('active', true)
                ->limit(10)
                ->get();

            $this->getBatchObject($fb, $objects, AdsInsightsLevelValues::CAMPAIGN);

            sleep(10);


            $objects = Content::whereNull('last_report_run')
                ->where('account_id', $fbAccount->id)
                ->orWhereRaw("DATE(last_report_run) < CURDATE()")
                ->whereNotNull('map_user_id')
                ->limit(10)
                ->get();

            $this->getBatchObject($fb, $objects, AdsInsightsLevelValues::ACCOUNT);

            $this->deactiveInsight();

        }
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Element;
use App\Models\Report;
use Carbon\Carbon;
use DB;
use FacebookAds\Object\Values\AdsInsightsLevelValues;
use Facebook\Facebook;
use Illuminate\Console\Command;

class DateInsight extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'date:insight';

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

    private function getInsightFields()
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


    private function addToReport($insight)
    {

        $socialId = null;

        if (!empty($insight['ad_id'])) {
            $socialId = $insight['ad_id'];
        } elseif (!empty($insight['adset_id'])) {
            $socialId = $insight['adset_id'];
        } elseif (!empty($insight['campaign_id'])) {
            $socialId = $insight['campaign_id'];
        }

        if ($socialId) {
            $element = Element::where('social_id', $socialId)->get();

            if ($element->count() > 0) {
                $element = $element->first();
                $insightDate = Carbon::parse($insight['date_start'])->toDateString();
                $result = 0;
                $cost_per_result = 0;
                if (in_array($insight['objective'], array_keys(config('system.insight.map'))) && isset($insight['actions'])) {
                    foreach ($insight['actions'] as $action) {
                        if ($action['action_type'] == config('system.insight.map.'.$insight['objective'])) {
                            $result = $action['value'];
                            break;
                        }
                    }
                }

                if (in_array($insight['objective'], array_keys(config('system.insight.map'))) && isset($insight['cost_per_action_type'])) {
                    foreach ($insight['cost_per_action_type'] as $action) {
                        if ($action['action_type'] == config('system.insight.map.'.$insight['objective'])) {
                            $cost_per_result = $action['value'];
                            break;
                        }
                    }
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

    private function batchInsight($elements)
    {

        $token = $elements->first()->content->account->api_token;

        $fb = new Facebook([
            'app_id' => config('system.facebook.app_id'),
            'app_secret' =>  config('system.facebook.app_secret'),
            'default_graph_version' => 'v2.11',
            'default_access_token' => $token
        ]);


        $start_date = Carbon::now()->subDays(7)->toDateString();
        $end_date = Carbon::now()->toDateString();


        $requests = [];
        DB::beginTransaction();
        $elementIds = $elements->pluck('id')->all();
        try {
            foreach ($elements as $element) {

                $levelType = null;

                if ($element->social_level == config('system.insight.types.campaign')) {
                    $levelType = AdsInsightsLevelValues::CAMPAIGN;
                }

                if ($element->social_level == config('system.insight.types.adset')) {
                    $levelType = AdsInsightsLevelValues::ADSET;
                }

                if ($element->social_level == config('system.insight.types.ad')) {
                    $levelType = AdsInsightsLevelValues::AD;
                }

                $params = [
                    'time_range' => [
                        "since" => $start_date,
                        "until" => $end_date
                    ],
                    'time_increment' => 1,
                    'level' => $levelType,
                    'fields' => implode(',', $this->getInsightFields())
                ];

                $requests[] = $fb->request('GET', '/'.$element->social_id.'/insights', $params);
            }


            if ($requests) {

                $responses = $fb->sendBatchRequest($requests);

                foreach ($responses as $key => $response) {
                    if ($response->isError()) {
                        $e = $response->getThrownException();
                        $this->line($e->getMessage());
                        $this->line("KEY=".$key);
                    } else {
                        $this->line('Working with Fb Response..');
                        $content = json_decode($response->getBody(), true);
                        if (!empty($content['data'])) {
                            foreach ($content['data'] as $insight) {
                                $this->addToReport($insight);
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
            $this->line($e->getMessage());
        }



    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        #start get insight for today.

        $activeElements = Element::where('social_status', true)
            ->where('social_type', config('system.social_type.facebook'))
           /* ->where(function($q){
                $q->whereRaw('DATE(last_insight_updated) < CURDATE()');
                $q->orWhereNull('last_insight_updated');
            })*/
            ->get();


        # for elements for one account we choose 50
        if ($activeElements->count() > 0) {
            //dd($activeElements->count());
            foreach ($activeElements->groupBy('content_id') as $groupElements) {
               foreach ($groupElements->chunk(50) as $elements) {
                   $this->batchInsight($elements);
               }
            }
        }

    }
}

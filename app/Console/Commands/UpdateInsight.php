<?php

namespace App\Console\Commands;

use App\Models\AdAccount;
use App\Models\AdAd;
use App\Models\AdAdSet;
use App\Models\AdCampaign;
use App\Models\FbAccount;
use App\Models\Insight;
use Carbon\Carbon;
use DB;
use FacebookAds\Object\AdSet;
use Illuminate\Console\Command;

class UpdateInsight extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:insight';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update insight';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }



    public function read($object, $type, $fields, $params, $user_id = null, $fb_account_id = null, $ad_account_id = null, $ad_campaign_id = null, $ad_adset_id = null)
    {
        $contentId = ($type == config('system.insight.types.account')) ? $object->account_id : $object->id;

        if ($type == config('system.insight.types.account')) {
            AdAccount::updateOrCreate(['id' => $contentId], [
                'user_id' => $user_id,
                'fb_account_id' => $fb_account_id,
                'name' => $object->name
            ]);
        } elseif ($type == config('system.insight.types.campaign')) {
            AdCampaign::updateOrCreate(['id' => $contentId], [
                'user_id' => $user_id,
                'fb_account_id' => $fb_account_id,
                'ad_account_id' => $ad_account_id,
                'name' => $object->name
            ]);
        } elseif ($type == config('system.insight.types.adset')) {
            AdAdSet::updateOrCreate(['id' => $contentId], [
                'user_id' => $user_id,
                'fb_account_id' => $fb_account_id,
                'ad_account_id' => $ad_account_id,
                'ad_campaign_id' => $ad_campaign_id,
                'name' => $object->name
            ]);
        } elseif ($type == config('system.insight.types.ad')) {
            AdAd::updateOrCreate(['id' => $contentId], [
                'user_id' => $user_id,
                'fb_account_id' => $fb_account_id,
                'ad_account_id' => $ad_account_id,
                'ad_campaign_id' => $ad_campaign_id,
                'ad_adset_id' => $ad_adset_id,
                'name' => $object->name
            ]);
        }

        foreach ($object->getInsights($fields, $params) as $insight) {
            Insight::updateOrCreate([
                'content_id' => $contentId,
                'content_type' => $type,
                'date' => Carbon::parse($insight->date_start)->toDateString()
            ], [
                'user_id' => $user_id,
                'fb_account_id' => $fb_account_id,
                'account_currency' => $insight->account_currency,
                'account_id' => $insight->account_id,
                'account_name'=> $insight->account_name,
                'ad_id'=> $insight->ad_id,
                'ad_name'=> $insight->ad_name,
                'adset_id'=> $insight->adset_id,
                'adset_name'=> $insight->adset_name,
                'buying_type'=> $insight->buying_type,
                'campaign_id'=> $insight->campaign_id,
                'campaign_name'=> $insight->campaign_name,
                'clicks'=> $insight->clicks,
                'cpc'=> $insight->cpc,
                'cpm'=> $insight->cpm,
                'cpp'=> $insight->cpp,
                'ctr'=> $insight->ctr,
                'impressions'=> $insight->impressions,
                'inline_link_click_ctr'=> $insight->inline_link_click_ctr,
                'inline_link_clicks'=> $insight->inline_link_clicks,
                'inline_post_engagement'=> $insight->inline_post_engagement,
                'reach'=> $insight->reach,
                'social_clicks'=> $insight->social_clicks,
                'social_impressions'=> $insight->social_impressions,
                'social_reach'=> $insight->social_reach,
                'social_spend'=> $insight->social_spend,
                'spend'=> $insight->spend,
                'unique_clicks'=> $insight->unique_clicks,
                'unique_ctr'=> $insight->unique_ctr,
                'unique_inline_link_click_ctr'=> $insight->unique_inline_link_click_ctr,
                'unique_inline_link_clicks'=> $insight->unique_inline_link_clicks,
                'unique_link_clicks_ctr'=> $insight->unique_link_clicks_ctr,
                'unique_social_clicks'=> $insight->unique_social_clicks,
            ]);
        }

    }

    /**
     * @param $fbAccount
     */
    public function insight($fbAccount)
    {

        DB::beginTransaction();
        try {
            // Initialize a new Session and instanciate an Api object
            \FacebookAds\Api::init(config('system.facebook.app_id'), config('system.facebook.app_secret'), $fbAccount->fb_token);

            // The Api object is now available trough singleton
            $api = \FacebookAds\Api::instance();

            if ($fbAccount->is_filled_old_data) {
                $start_date = Carbon::now()->toDateString();
                $end_date = Carbon::now()->toDateString();
            } else {
                //$start_date = "2016-07-12";
                //$end_date = "2016-07-19";
                $start_date = Carbon::now()->startOfMonth()->toDateString();
                $end_date = Carbon::now()->toDateString();
            }

            $fields = [
                'account_currency',
                'account_id',
                'account_name',
                'ad_id',
                'ad_name',
                'adset_id',
                'adset_name',
                'buying_type',
                'campaign_id',
                'campaign_name',
                'clicks',
                'cpc',
                'cpm',
                'cpp',
                'ctr',
                'impressions',
                'inline_link_click_ctr',
                'inline_link_clicks',
                'inline_post_engagement',
                'reach',
                'social_clicks',
                'social_impressions',
                'social_reach',
                'social_spend',
                'spend',
                'unique_clicks',
                'unique_ctr',
                'unique_inline_link_click_ctr',
                'unique_inline_link_clicks',
                'unique_link_clicks_ctr',
                'unique_social_clicks',
            ];

            $params = [
                'time_range' => [
                    "since" => $start_date,
                    "until" => $end_date
                ],
                'time_increment' => 1
            ];

            $me = new \FacebookAds\Object\User($fbAccount->id);

            $accounts = $me->getAdAccounts([
                'account_id',
                'name'
            ]);

            foreach ($accounts as $account) {
                $this->read($account, config('system.insight.types.account'), $fields, $params, $fbAccount->user_id, $fbAccount->id);

                $campaigns = $account->getCampaigns([
                    'id',
                    'name'
                ]);

                foreach ($campaigns as $campaign) {
                    $this->read($campaign, config('system.insight.types.campaign'), $fields, $params, $fbAccount->user_id, $fbAccount->id,$account->account_id);
                    $adSets = $campaign->getAdSets([
                        'id',
                        'name'
                    ]);
                    foreach ($adSets as $adSet) {
                        $this->read($adSet, config('system.insight.types.adset'), $fields, $params, $fbAccount->user_id, $fbAccount->id,$account->account_id, $campaign->id);
                        $ads = $adSet->getAds([
                            'id',
                            'name'
                        ]);
                        foreach ($ads as $ad) {
                            $this->read($ad, config('system.insight.types.ad'), $fields, $params, $fbAccount->user_id, $fbAccount->id,$account->account_id, $campaign->id, $adSet->id);
                        }
                    }
                }
            }

            $fbAccount->update([
                'is_filled_old_data' => true
            ]);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
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
        $fbAccounts = FbAccount::all();
        foreach ($fbAccounts as $fbAccount) {
            $this->insight($fbAccount);
        }
    }
}

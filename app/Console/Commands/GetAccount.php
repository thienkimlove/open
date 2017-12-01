<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\Ad;
use App\Models\Campaign;
use App\Models\Content;
use App\Models\Insight;
use App\Models\Set;
use Carbon\Carbon;
use DB;
use FacebookAds\Api;
use FacebookAds\Object\User;
use FacebookAds\Object\Values\AdsInsightsLevelValues;
use Illuminate\Console\Command;

class GetAccount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:account';

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

    public function getAdAccountFields() {
        return array(
            'account_id',
            'name',
            'amount_spent',
            'balance',
            'currency',
            'min_campaign_group_spend_cap',
            'min_daily_budget',
            'next_bill_date',
            'spend_cap',
        );
    }
    public function getCampaignFields() {
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
        );
    }

    public function getCampaignsForAdAccount($adAccount, $content)
    {
        if ($content->social_id == 111084382616439) {
            $start_date = "2016-07-12";
            $end_date = "2016-07-19";
        } else {
            $start_date = Carbon::now()->toDateString();
            $end_date = Carbon::now()->toDateString();
        }

        $params = [
            'time_range' => [
                "since" => $start_date,
                "until" => $end_date
            ],
            //'time_increment' => 1
        ];

        $fields = $this->getCampaignFields();
        $campaigns = $adAccount->getCampaigns($fields, $params);
        $currentMapUserId = Content::whereNotNull('map_user_id')->pluck('map_user_id')->all();

        //de active all campaign not longer map to a users and current user.
        Campaign::where('active', true)
            ->whereNotIn('user_id', $currentMapUserId)
            ->orWhere('user_id', $content->map_user_id)
            ->update(['active' => false]);

        foreach ($campaigns as $campaign) {

            Campaign::updateOrCreate([
                'social_id' => $campaign->id,
                'social_type' => config('system.social_type.facebook')
            ], [
                'user_id' => $content->map_user_id,
                'account_id' => $content->account_id,
                'content_id' => $content->id,
                'social_name' => $campaign->name,
                'social_account_id' => $campaign->account_id,
                'boosted_object_id' => $campaign->boosted_object_id,
                'buying_type' => $campaign->buying_type,
                'created_time' => $campaign->created_time,
                'objective' => $campaign->objective,
                'start_time' => $campaign->start_time,
                'stop_time' => $campaign->stop_time,
                'updated_time' => $campaign->updated_time,
                'active' => true
            ]);

        }

    }

    public function getAdSetFields() {
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
        );
    }

    private function getAdSetsForAdAccount($adAccount, $content)
    {
        $fields = $this->getAdSetFields();

        if ($content->social_id == 111084382616439) {
            $start_date = "2016-07-12";
            $end_date = "2016-07-19";
        } else {
            $start_date = Carbon::now()->toDateString();
            $end_date = Carbon::now()->toDateString();
        }

        $params = [
            'time_range' => [
                "since" => $start_date,
                "until" => $end_date
            ],
            //'time_increment' => 1
        ];

        $sets = $adAccount->getAdSets($fields, $params);

        $currentMapUserId = Content::whereNotNull('map_user_id')->pluck('map_user_id')->all();

        //de active all sets not longer map to a users and current user.

        Set::where('active', true)
            ->whereNotIn('user_id', $currentMapUserId)
            ->orWhere('user_id', $content->map_user_id)
            ->update(['active' => false]);

        foreach ($sets as $set) {

            $campaignForSet = Campaign::where('social_id', $set->campaign_id)->where('social_type',  config('system.social_type.facebook'))->get();

            if ($campaignForSet->count() > 0) {

                Set::updateOrCreate([
                    'social_id' => $set->id,
                    'social_type' => config('system.social_type.facebook')
                ], [
                    'user_id' => $campaignForSet->first()->user_id,
                    'account_id' => $campaignForSet->first()->account_id,
                    'content_id' => $campaignForSet->first()->content_id,
                    'campaign_id' => $campaignForSet->first()->id,

                    'social_name' => $set->name,

                    'social_account_id' => $set->account_id,
                    'budget_remaining' => $set->budget_remaining,
                    'social_campaign_id' => $set->campaign_id,
                    'created_time' => $set->created_time,
                    'daily_budget' => $set->daily_budget,
                    'destination_type' => $set->destination_type,
                    'end_time' => $set->end_time,
                    'lifetime_budget' => $set->lifetime_budget,
                    'lifetime_imps' => $set->lifetime_imps,
                    'start_time' => $set->start_time,
                    'updated_time' => $set->updated_time,
                    'active' => true
                ]);
            }
        }

    }
    public function getAdFields() {
        return array(
            'account_id',
            'adset_id',
            'campaign_id',
            'created_time',
            'id',
            'name',
            'updated_time',
        );
    }

    public function getAdsForAdAccount($adAccount, $content)
    {
        $fields = $this->getAdFields();

        if ($content->social_id == 111084382616439) {
            $start_date = "2016-07-12";
            $end_date = "2016-07-19";
        } else {
            $start_date = Carbon::now()->toDateString();
            $end_date = Carbon::now()->toDateString();
        }

        $params = [
            'time_range' => [
                "since" => $start_date,
                "until" => $end_date
            ],
            //'time_increment' => 1
        ];

        $ads = $adAccount->getAds($fields, $params);


        $currentMapUserId = Content::whereNotNull('map_user_id')->pluck('map_user_id')->all();

        //de active all sets not longer map to a users and current user.

        Ad::where('active', true)
            ->whereNotIn('user_id', $currentMapUserId)
            ->orWhere('user_id', $content->map_user_id)
            ->update(['active' => false]);


        foreach ($ads as $ad) {

            $setForAd = Set::where('social_id', $ad->adset_id)->where('social_type',  config('system.social_type.facebook'))->get();

            if ($setForAd->count() > 0) {

               Ad::updateOrCreate([
                    'social_id' => $ad->id,
                    'social_type' => config('system.social_type.facebook')
                ], [
                    'user_id' => $setForAd->first()->user_id,
                    'account_id' => $setForAd->first()->account_id,
                    'content_id' => $setForAd->first()->content_id,
                    'campaign_id' => $setForAd->first()->campaign_id,
                    'set_id' => $setForAd->first()->id,

                    'social_name' => $ad->name,
                    'social_account_id' => $ad->account_id,
                    'social_campaign_id' => $ad->campaign_id,
                    'social_adset_id' => $ad->adset_id,
                    'created_time' => $ad->created_time,
                    'updated_time' => $ad->updated_time,
                    'active' => true
                ]);
            }
        }
    }

    public function getInsightFields()
    {
        return [
            'account_id',
            'ad_id',
            'adset_id',
            'campaign_id',

            'date_start',

            'clicks',
            'impressions',
            'reach',
            'spend',
        ];
    }

    private function getAdAccounts($fbAccount)
    {
        try {
            DB::beginTransaction();
            Api::init(config('system.facebook.app_id'), config('system.facebook.app_secret'), $fbAccount->api_token);
            Api::instance();
            $me = new User($fbAccount->social_id);

            $fields = $this->getAdAccountFields();
            $accounts = $me->getAdAccounts($fields);
            foreach ($accounts as $account) {

                $content = Content::updateOrCreate([
                    'social_id' => $account->account_id,
                    'social_type' => config('system.social_type.facebook')
                ], [
                    'user_id' => $fbAccount->user_id,
                    'account_id' => $fbAccount->id,
                    'social_name' => $account->name,
                    'amount_spent' => $account->amount_spent,
                    'balance' => $account->balance,
                    'currency' => $account->currency,
                    'min_campaign_group_spend_cap' => $account->min_campaign_group_spend_cap,
                    'min_daily_budget' => $account->min_daily_budget,
                    'next_bill_date' => $account->next_bill_date,
                    'spend_cap' => $account->spend_cap,
                ]);

                if ($content->map_user_id) {
                    $this->getCampaignsForAdAccount($account, $content);
                    $this->getAdSetsForAdAccount($account, $content);
                    $this->getAdsForAdAccount($account, $content);
                }

            }
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
        $fbAccounts = Account::where('social_type', config('system.social_type.facebook'))->get();
        foreach ($fbAccounts as $fbAccount) {
            $this->getAdAccounts($fbAccount);
        }

    }
}

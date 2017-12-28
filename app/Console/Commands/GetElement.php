<?php

namespace App\Console\Commands;

use App\Models\Content;
use App\Models\Element;
use Carbon\Carbon;
use FacebookAds\Object\AdAccount;
use Illuminate\Console\Command;
use FacebookAds\Api;


class GetElement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:element';

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
            'status',
        );
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
            'status',
        );
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
            'status',
        );
    }


    private function fetchAccountElements($adAccount)
    {
        # Get element  for account today.
        $start_date = Carbon::now()->toDateString();
        $end_date = Carbon::now()->toDateString();

        $socialAccount = null;

        $params = [
            'time_range' => [
                "since" => $start_date,
                "until" => $end_date
            ]
        ];

        Api::init(config('system.facebook.app_id'), config('system.facebook.app_secret'), $adAccount->account->api_token);
        Api::instance();

        try {
            $socialAccount = new AdAccount('act_'.$adAccount->social_id);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            $adAccount->update([
                'status' => false
            ]);

        }

        if ($socialAccount) {
            $campaignFields = $this->getCampaignFields();
            $adSetFields = $this->getAdSetFields();
            $adFields = $this->getAdFields();

            foreach ($socialAccount->getCampaigns($campaignFields, $params) as $campaign) {

                $data = [];

                foreach ($campaignFields as $field) {
                    $data[$field] = $campaign->{$field};
                }

                Element::updateOrCreate([
                    'social_id' => $campaign->id,
                    'social_type' => config('system.social_type.facebook'),
                    'social_level' => config('system.insight.types.campaign')
                ], [
                    'content_id' => $adAccount->id,
                    'social_name' => $campaign->name,
                    'social_parent' => $campaign->account_id,
                    'social_status' => ($campaign->status == 'ACTIVE') ? true : false,
                    'json_data' => json_encode($data, true)
                ]);

            }

            foreach ($socialAccount->getAdSets($adSetFields, $params) as $adSet) {

                $data = [];

                foreach ($adSetFields as $field) {
                    $data[$field] = $adSet->{$field};
                }

                Element::updateOrCreate([
                    'social_id' => $adSet->id,
                    'social_type' => config('system.social_type.facebook'),
                    'social_level' => config('system.insight.types.adset')
                ], [
                    'content_id' => $adAccount->id,
                    'social_name' => $adSet->name,
                    'social_parent' => $adSet->campaign_id,
                    'social_status' => ($adSet->status == 'ACTIVE') ? true : false,
                    'json_data' => json_encode($data, true)
                ]);

            }



            foreach ($socialAccount->getAds($adFields, $params) as $ad) {

                $data = [];

                foreach ($adFields as $field) {
                    $data[$field] = $ad->{$field};
                }

                Element::updateOrCreate([
                    'social_id' => $ad->id,
                    'social_type' => config('system.social_type.facebook'),
                    'social_level' => config('system.insight.types.ad')
                ], [
                    'content_id' => $adAccount->id,
                    'social_name' => $ad->name,
                    'social_parent' => $ad->adset_id,
                    'social_status' => ($ad->status == 'ACTIVE') ? true : false,
                    'json_data' => json_encode($data, true)
                ]);

            }

        }


    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        #For facebook only

        $adAccounts = Content::where('status', true)
            ->whereNotNull('user_id')
            ->where('social_type', config('system.social_type.facebook'))
            ->get();

        foreach ($adAccounts as $adAccount) {
            $this->fetchAccountElements($adAccount);
        }
    }
}

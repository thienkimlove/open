<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableInsights extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('insights', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('content_id')->index();
            $table->unsignedSmallInteger('content_type')->index();
            $table->date('date')->index();

            //list of insight fields
            $table->string('account_currency')->nullable();
            $table->string('account_id')->nullable();
            $table->string('account_name')->nullable();
            $table->string('ad_id')->nullable();
            $table->string('ad_name')->nullable();
            $table->string('adset_id')->nullable();
            $table->string('adset_name')->nullable();

            $table->string('buying_type')->nullable();
            $table->string('campaign_id')->nullable();
            $table->string('campaign_name')->nullable();
            $table->string('clicks')->nullable();
            $table->string('cpc')->nullable();
            $table->string('cpm')->nullable();
            $table->string('cpp')->nullable();
            $table->string('ctr')->nullable();


            $table->string('impressions')->nullable();
            $table->string('inline_link_click_ctr')->nullable();
            $table->string('inline_link_clicks')->nullable();
            $table->string('inline_post_engagement')->nullable();
            $table->string('reach')->nullable();

            $table->string('social_clicks')->nullable();
            $table->string('social_impressions')->nullable();
            $table->string('social_reach')->nullable();
            $table->string('social_spend')->nullable();
            $table->string('spend')->nullable();

            $table->string('unique_clicks')->nullable();
            $table->string('unique_ctr')->nullable();
            $table->string('unique_inline_link_click_ctr')->nullable();
            $table->string('unique_inline_link_clicks')->nullable();
            $table->string('unique_link_clicks_ctr')->nullable();
            $table->string('unique_social_clicks')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('insights');
    }
}

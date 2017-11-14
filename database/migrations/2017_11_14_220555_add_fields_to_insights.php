<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToInsights extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('insights', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->index();
            $table->unsignedBigInteger('fb_account_id')->index();
        });
        Schema::table('ad_accounts', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->index();
        });

        Schema::table('ad_campaigns', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->index();
            $table->unsignedBigInteger('fb_account_id')->index();
        });

        Schema::table('ad_adsets', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->index();
            $table->unsignedBigInteger('fb_account_id')->index();
            $table->unsignedBigInteger('ad_account_id')->index();
        });

        Schema::table('ad_ads', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->index();
            $table->unsignedBigInteger('fb_account_id')->index();
            $table->unsignedBigInteger('ad_account_id')->index();
            $table->unsignedBigInteger('ad_campaign_id')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('insights', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'fb_account_id']);
        });
        Schema::table('ad_accounts', function (Blueprint $table) {
            $table->dropColumn(['user_id']);
        });
        Schema::table('ad_campaigns', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'fb_account_id']);
        });

        Schema::table('ad_adsets', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'fb_account_id', 'ad_account_id']);
        });

        Schema::table('ad_ads', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'fb_account_id', 'ad_account_id', 'ad_campaign_id']);
        });
    }
}

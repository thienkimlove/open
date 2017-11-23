<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToAds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ads', function (Blueprint $table) {
            $table->string('social_account_id')->nullable();
            $table->string('social_campaign_id')->nullable();
            $table->string('social_adset_id')->nullable();
            $table->string('created_time')->nullable();
            $table->string('updated_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ads', function (Blueprint $table) {
            $table->dropColumn([
                'social_account_id',
                'social_campaign_id',
                'social_adset_id',
                'created_time',
                'updated_time',
            ]);
        });
    }
}

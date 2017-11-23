<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdFieldTypeToInsights extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('insights', function (Blueprint $table) {
            $table->unsignedTinyInteger('object_type')->index();
            $table->unsignedTinyInteger('social_type')->index();
            $table->string('social_account_id')->nullable();
            $table->string('social_campaign_id')->nullable();
            $table->string('social_adset_id')->nullable();
            $table->string('social_ad_id')->nullable();
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
            $table->dropColumn('object_type');
            $table->dropColumn('social_type');
            $table->dropColumn('social_account_id');
            $table->dropColumn('social_campaign_id');
            $table->dropColumn('social_adset_id');
            $table->dropColumn('social_ad_id');
        });
    }
}

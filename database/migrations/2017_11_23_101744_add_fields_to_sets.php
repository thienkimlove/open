<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToSets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sets', function (Blueprint $table) {
            $table->string('social_account_id')->nullable();
            $table->string('budget_remaining')->nullable();
            $table->string('social_campaign_id')->nullable();
            $table->string('created_time')->nullable();
            $table->string('daily_budget')->nullable();
            $table->string('destination_type')->nullable();
            $table->string('end_time')->nullable();
            $table->string('lifetime_budget')->nullable();
            $table->string('lifetime_imps')->nullable();
            $table->string('start_time')->nullable();
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
        Schema::table('sets', function (Blueprint $table) {
            $table->dropColumn([
                'social_account_id',
                'budget_remaining',
                'social_campaign_id',
                'created_time',
                'daily_budget',
                'destination_type',
                'end_time',
                'lifetime_budget',
                'lifetime_imps',
                'start_time',
                'updated_time',
            ]);
        });
    }
}

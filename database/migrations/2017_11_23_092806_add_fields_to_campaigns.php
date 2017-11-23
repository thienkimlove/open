<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToCampaigns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->string('social_account_id')->nullable();
            $table->string('boosted_object_id')->nullable();
            $table->string('buying_type')->nullable();
            $table->string('created_time')->nullable();
            $table->string('objective')->nullable();
            $table->string('start_time')->nullable();
            $table->string('stop_time')->nullable();
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
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn([
                'social_account_id',
                'boosted_object_id',
                'buying_type',
                'created_time',
                'objective',
                'start_time',
                'stop_time',
                'updated_time',
            ]);
        });
    }
}

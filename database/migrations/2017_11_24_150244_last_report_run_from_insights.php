<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LastReportRunFromInsights extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->dateTime('last_report_run')->nullable()->default(null);
        });

        Schema::table('campaigns', function (Blueprint $table) {
            $table->dateTime('last_report_run')->nullable()->default(null);
        });

        Schema::table('sets', function (Blueprint $table) {
            $table->dateTime('last_report_run')->nullable()->default(null);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->dropColumn('last_report_run');
        });

        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn('last_report_run');
        });


        Schema::table('sets', function (Blueprint $table) {
            $table->dropColumn('last_report_run');
        });
    }
}

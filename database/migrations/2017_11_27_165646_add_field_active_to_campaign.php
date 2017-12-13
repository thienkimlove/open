<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldActiveToCampaign extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->boolean('active')->default(true);
        });

        Schema::table('sets', function (Blueprint $table) {
            $table->boolean('active')->default(true);
        });

        Schema::table('ads', function (Blueprint $table) {
            $table->boolean('active')->default(true);
        });

        Schema::table('insights', function (Blueprint $table) {
            $table->boolean('active')->default(true);
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
            $table->dropColumn('active');
        });

        Schema::table('sets', function (Blueprint $table) {
            $table->dropColumn('active');
        });

        Schema::table('ads', function (Blueprint $table) {
            $table->dropColumn('active');
        });

        Schema::table('insights', function (Blueprint $table) {
            $table->dropColumn('active');
        });
    }
}

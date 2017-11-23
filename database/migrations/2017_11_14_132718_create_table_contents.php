<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableContents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->index();
            $table->unsignedInteger('account_id')->index();
            $table->string('social_id');
            $table->string('social_name');
            $table->unsignedTinyInteger('social_type');


            //attributes for account_level

            $table->string('amount_spent')->nullable();
            $table->string('balance')->nullable();
            $table->string('currency')->nullable();
            $table->string('min_campaign_group_spend_cap')->nullable();
            $table->string('min_daily_budget')->nullable();
            $table->string('next_bill_date')->nullable();
            $table->string('spend_cap')->nullable();


            $table->boolean('status')->default(true);
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
        Schema::dropIfExists('contents');
    }
}

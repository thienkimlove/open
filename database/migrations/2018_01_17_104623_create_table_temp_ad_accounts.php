<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTempAdAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_ad_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('social_id');
            $table->unsignedBigInteger('account_id');
            $table->unsignedSmallInteger('social_type');
            $table->string('social_name');
            $table->string('currency');
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
        Schema::dropIfExists('temp_ad_accounts');
    }
}

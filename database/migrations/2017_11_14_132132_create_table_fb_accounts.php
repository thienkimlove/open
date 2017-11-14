<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableFbAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fb_accounts', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->unsignedInteger('user_id')->index();
            $table->string('fb_token')->nullable()->default(null);
            $table->dateTime('fb_token_start')->nullable()->default(null);
            $table->boolean('is_filled_old_data')->default(false);
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
        Schema::dropIfExists('fb_accounts');
    }
}

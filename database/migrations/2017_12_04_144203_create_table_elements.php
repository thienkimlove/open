<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableElements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('elements', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('content_id')->index();
            $table->unsignedBigInteger('social_id')->index();
            $table->unsignedTinyInteger('social_type')->index();
            $table->unsignedTinyInteger('social_level')->index();
            $table->string('social_name');
            $table->unsignedBigInteger('social_parent')->nullable();
            $table->boolean('social_status')->default(true);
            $table->dateTime('last_insight_updated')->nullable()->default(null);
            $table->longText('json_data')->nullable();

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
        Schema::dropIfExists('elements');
    }
}

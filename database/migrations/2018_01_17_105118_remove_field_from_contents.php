<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveFieldFromContents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->dropColumn([
                'amount_spent',
                'balance',
                'min_campaign_group_spend_cap',
                'next_bill_date',
                'spend_cap',
                'spend_cap',
            ]);

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
            $table->string('amount_spent')->nullable();
            $table->string('balance')->nullable();
            $table->string('min_campaign_group_spend_cap')->nullable();
            $table->string('min_daily_budget')->nullable();
            $table->string('next_bill_date')->nullable();
            $table->string('spend_cap')->nullable();
        });
    }
}

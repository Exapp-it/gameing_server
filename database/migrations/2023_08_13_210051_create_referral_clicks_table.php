<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferralClicksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referral_clicks', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->ipAddress('ip');
            $table->string('domain')->nullable();
            $table->timestamps();

            $table->index('user_id', 'click_user_idx');
            $table->foreign('user_id', 'click_user_fk')
                ->on('users')
                ->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('referral_clicks');
    }
}

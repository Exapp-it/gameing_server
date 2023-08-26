<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('referrer_id');
            $table->unsignedInteger('referral_id');
            $table->dateTime('joined_at')->nullable();
            $table->timestamps();

            $table->index('referrer_id', 'referrer_user_idx');
            $table->foreign('referrer_id', 'referrer_user_fk')
                ->on('users')
                ->references('id');

            $table->index('referral_id', 'referral_user_idx');
            $table->foreign('referral_id', 'referral_user_fk')
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
        Schema::dropIfExists('referrals');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAffiliateProgramsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('affiliate_programs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('referral_id');
            $table->double('amount');
            $table->boolean('completed');
            $table->timestamps();

            $table->index('user_id', 'affiliate_programs_users_idx');
            $table->foreign('user_id', 'affiliate_programs_users_fk')
                ->on('users')
                ->references('id');

            $table->index('referral_id', 'affiliate_programs_referral_idx');
            $table->foreign('referral_id', 'affiliate_programs_referral_fk')
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
        Schema::dropIfExists('affiliate_programs');
    }
}

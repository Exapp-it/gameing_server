<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserBonusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_bonuses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('bonus_id');
            $table->unsignedInteger('user_id');
            $table->tinyInteger('provider');
            $table->double('win')->nullable();
            $table->double('round_win')->nullable();
            $table->bigInteger('round_id')->nullable();
            $table->integer('count')->nullable();
            $table->integer('played')->nullable();
            $table->dateTime('start')->default(date("Y-m-d H:i:s"));
            $table->dateTime('end')->nullable();
            $table->boolean('status')->default(true);

            $table->index('user_id', 'bonus_user_idx');
            $table->foreign('user_id', 'bonus_user_fk')
                ->on('users')
                ->references('id');

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
        Schema::dropIfExists('user_bonuses');
    }
}

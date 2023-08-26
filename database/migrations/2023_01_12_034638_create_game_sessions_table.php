<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGameSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_sessions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->dateTime('start')->default(date("Y-m-d H:i:s"));
            $table->dateTime('end')->nullable();
            $table->integer('type');
            $table->string('session_id');

            $table->index('user_id', 'game_sessions_users_idx');
            $table->foreign('user_id', 'game_sessions_users_fk')
                ->on('users')
                ->references('id');

            $table->softDeletes();
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
        Schema::dropIfExists('game_sessions');
    }
}

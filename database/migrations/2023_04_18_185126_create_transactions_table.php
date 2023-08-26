<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->softDeletes();
            $table->double('amount');
            $table->string('currency')->nullable();
            $table->unsignedInteger('user_id');
            $table->string('reference')->unique();
            $table->unsignedInteger('game_session_id')->nullable();
            $table->string('game_round_id')->nullable();
            $table->unsignedInteger('game_id')->nullable();
            $table->enum('type', array_values(config('enums.transaction_types')));
            $table->boolean('completed');

            $table->index('user_id', 'transactions_users_idx');
            $table->foreign('user_id', 'transactions_users_fk')
                ->on('users')
                ->references('id');

            $table->index('game_session_id', 'transactions_game_sessions_idx');
            $table->foreign('game_session_id', 'transactions_game_sessions_fk')
                ->on('game_sessions')
                ->references('id');

            $table->index('game_id', 'transactions_games_idx');
            $table->foreign('game_id', 'transactions_games_fk')
                ->on('games')
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
        Schema::dropIfExists('transactions');
    }
}

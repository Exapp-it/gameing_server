<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWithdrawsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withdraws', function (Blueprint $table) {
            $table->increments('id');
            $table->softDeletes();
            $table->unsignedInteger('user_id');
            $table->enum(
                'status', 
                array_values(config('enums.transaction_status'))
            )->default("CREATED");
            $table->string('internal_id')->nullable();
            $table->float('amount');
            $table->string('fiat_address');
            $table->boolean('confirmed')->default(false);
            $table->string('currency');

            $table->index('user_id', 'withdraw_user_idx');
            $table->foreign('user_id', 'withdraw_user_fk')
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
        Schema::dropIfExists('withdraws');
    }
}

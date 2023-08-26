<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');
            $table->softDeletes();
            $table->float('amount');
            $table->unsignedInteger('user_id');
            $table->string('currency');
            $table->enum(
                'status', 
                array_values(config('enums.transaction_status'))
            )->default("CREATED");
            $table->string('internal_id')->nullable();
            $table->timestamps();

            $table->index('user_id', 'payment_user_idx');

            $table->foreign('user_id', 'payment_user_fk')
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
        Schema::dropIfExists('payments');
    }
}

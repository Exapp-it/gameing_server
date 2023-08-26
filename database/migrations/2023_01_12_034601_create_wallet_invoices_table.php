<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalletInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallet_invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->softDeletes();
            $table->float('amount');
            $table->enum(
                'status', 
                array_values(config('enums.transaction_status'))
            )->default("CREATED");
            $table->string('internal_id')->nullable();
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
        Schema::dropIfExists('wallet_invoices');
    }
}

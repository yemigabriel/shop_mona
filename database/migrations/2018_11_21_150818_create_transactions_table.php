<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->increments('id');
            $table->string('order_identity');
            $table->string('customer_code');
            $table->float('amount', 10, 2);
            $table->string('reference');
            $table->string('channel')->nullable();
            $table->string('bank')->nullable();
            $table->string('card_type')->nullable();
            $table->string('last4')->nullable();
            $table->string('country_code')->nullable();
            $table->string('ip_address')->nullable();
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

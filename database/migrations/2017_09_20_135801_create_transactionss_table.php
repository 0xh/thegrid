<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionssTable extends Migration
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
            $table->integer('supplier_id')->references('id')->on('users');
            $table->integer('customer_id')->references('id')->on('users');
            $table->double('amount')->default(0);
            $table->integer('job_id')->references('id')->on('jobs');
            $table->integer('bid_id')->references('id')->on('bids');
            $table->integer('status')->default(0);
            $table->string('payment_type')->default('cod');
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

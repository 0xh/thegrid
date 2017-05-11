<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('category_id')->unsigned();
            $table->string('name');
            $table->text('image_url');
            $table->integer('gallery_id')->unsigned();
            $table->text('details');
            $table->double('price');
            $table->integer('currency_id')->unsigned();
            $table->string('lat');
            $table->string('lng');
            $table->string('location');
            $table->dateTime('date');
            $table->timestamps();
        });

        // Schema::table('jobs', function ($table) {
        //     $table->foreign('gallery_id')->references('id')->on('galleries');
        //     $table->foreign('currency_id')->references('id')->on('currencies');
        //     $table->foreign('category_id')->references('id')->on('job_categories');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jobs');
    }
}

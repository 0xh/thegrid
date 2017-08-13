<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobSkillTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_skill', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('job_id')->unsigned()->references('id')->on('jobs');
            $table->integer('skill_id')->unsigned()->references('id')->on('skills');
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
        Schema::dropIfExists('job_skill');
    }
}

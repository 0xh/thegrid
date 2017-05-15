<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('first_name');
            $table->string('middle_name');
            $table->string('last_name');
            $table->integer('country_calling_code_id')->unsigned();
            $table->string('phone_number')->unique();
            $table->string('bio');
            $table->date('birth_date');
            $table->string('profile_image_url');
            $table->timestamps();
        });

        // Schema::table('profiles', function ($table) {
        //     $table->foreign('user_id')->references('id')->on('users');
        //     $table->foreign('country_calling_code_id')->references('id')->on('country_calling_codes');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profiles');
    }
}

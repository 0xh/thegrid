<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->boolean('is_awarded')->default(false)->after('status');
            $table->boolean('is_accepted')->default(false)->after('is_awarded');
            $table->boolean('is_moving')->default(false)->after('is_accepted');
            $table->boolean('is_reviewed')->default(false)->after('is_moving');
            $table->boolean('is_completed')->default(false)->after('is_reviewed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jobs', function (Blueprint $table) {
            //
        });
    }
}

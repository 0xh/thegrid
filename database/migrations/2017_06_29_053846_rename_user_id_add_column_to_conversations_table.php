<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameUserIdAddColumnToConversationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('conversations', function ($table) {
            $table->dropForeign('conversations_user_id_foreign');
            $table->dropColumn('user_id');
            $table->integer('user_id_1')->references('id')->on('users')->after('job_id');
            $table->integer('user_id_2')->references('id')->on('users')->after('user_id_1');
            $table->boolean('is_deleted')->default(0)->after('user_id_2');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

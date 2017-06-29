<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropUserIdAndAlterMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('messages', function ($table) {
            $table->dropForeign('messages_user_id_foreign');
            $table->dropColumn('user_id');
            $table->integer('author_id')->references('id')->on('users')->after('conversation_id');
            $table->integer('recipient_id')->references('id')->on('users')->after('author_id');
            $table->boolean('is_deleted')->default(0)->after('status');
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

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFriendRelationship extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_relationship', function (Blueprint $table) {
            $table->increments('id');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('friend_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->softDeletes();
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
        Schema::table('user_relationship', function (Blueprint $table) {
            $table->dropForeign('user_relationship_user_id_foreign');
            $table->dropForeign('user_relationship_friend_id_foreign');
        });
        Schema::dropIfExists('user_relationship');
    }
}

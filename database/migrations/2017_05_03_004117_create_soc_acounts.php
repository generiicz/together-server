<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSocAcounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tw_users', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->string('token');
            $table->unsignedInteger('user_id');
            $table->timestamps();
        });

        Schema::table('tw_users', function(Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('fb_users', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->string('token');
            $table->unsignedInteger('user_id');
            $table->timestamps();
        });

        Schema::table('fb_users', function(Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fb_users', function (Blueprint $table) {
            $table->dropForeign('fb_users_user_id_foreign');
        });
        Schema::table('tw_users', function (Blueprint $table) {
            $table->dropForeign('tw_users_user_id_foreign');
        });
        Schema::dropIfExists('fb_users');
        Schema::dropIfExists('tw_users');
    }
}

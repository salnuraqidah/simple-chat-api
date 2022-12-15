<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterHChatAddUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('h_chat', function (Blueprint $table) {
            $table->foreignId('user_to')->after('m_chat_id')->constrained('users');
            $table->foreignId('user_from')->after('user_to')->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('h_chat', function (Blueprint $table) {
            $table->dropForeign(['user_to']);
            $table->dropForeign(['user_from']);
            $table->dropColumn(['user_to', 'user_from']);
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdToHistoryPointUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('history_point_users', function (Blueprint $table) {
            $table->bigIncrements('id')->first();
        });
    }
    
    public function down()
    {
        Schema::table('history_point_users', function (Blueprint $table) {
            $table->dropColumn('id');
        });
    }
    
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimestampsToHistoryPointUsersTable extends Migration
{
    public function up()
    {
        Schema::table('history_point_users', function (Blueprint $table) {
            // Menambahkan kedua kolom created_at & updated_at
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::table('history_point_users', function (Blueprint $table) {
            // Menghapus kedua kolom jika rollback
            $table->dropTimestamps();
        });
    }
}

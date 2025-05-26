<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTanggalToHistoryPointUsersTable extends Migration
{
    public function up()
    {
        Schema::table('history_point_users', function (Blueprint $table) {
            // tambahkan kolom tanggal (hanya menyimpan tanggal saja)
            $table->date('tanggal')
                  ->nullable()
                  ->after('perubahan_point');
        });
    }

    public function down()
    {
        Schema::table('history_point_users', function (Blueprint $table) {
            $table->dropColumn('tanggal');
        });
    }
}

<?php

// Migration: add_sisa_cuti_to_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSisaCutiToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Menambahkan kolom sisa_cuti tanpa nilai default dan nullable
            $table->integer('sisa_cuti')->nullable(); // Kolom bisa null
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Menghapus kolom sisa_cuti jika migrasi dibatalkan
            $table->dropColumn('sisa_cuti');
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveRoleIdAndSisaCutiFromUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('role_id');
        $table->dropColumn('sisa_cuti');
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->integer('role_id')->nullable();  // Tentukan tipe kolom dan nullable jika dibutuhkan
        $table->integer('sisa_cuti')->nullable(); // Tentukan tipe kolom dan nullable jika dibutuhkan
    });
}

}

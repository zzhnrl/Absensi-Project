<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('cutis', function (Blueprint $table) {
            // Tambahkan kolom sisa_cuti setelah kolom yang sesuai, misal setelah 'jumlah_cuti'
            $table->integer('sisa_cuti')
                  ->after('jumlah_cuti')
                  ->default(0)
                  ->comment('Sisa hari cuti yang belum terpakai');
        });
    }

    public function down()
    {
        Schema::table('cutis', function (Blueprint $table) {
            $table->dropColumn('sisa_cuti');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('cutis', function (Blueprint $table) {
            // Tambahkan kolom jumlah_cuti sebelum sisa_cuti (atau setelah kolom yang Anda inginkan)
            $table->integer('jumlah_cuti')
                  ->after('some_existing_column')
                  ->default(0)
                  ->comment('Total hari cuti yang diajukan');
        });
    }

    public function down()
    {
        Schema::table('cutis', function (Blueprint $table) {
            $table->dropColumn('jumlah_cuti');
        });
    }
};

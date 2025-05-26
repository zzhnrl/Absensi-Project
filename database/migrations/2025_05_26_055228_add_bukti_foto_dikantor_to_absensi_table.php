<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->string('bukti_foto_dikantor')
                  ->nullable()
                  ->after('lokasi'); // sesuaikan kolom sebelumnya
        });
    }

    public function down()
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropColumn('bukti_foto_dikantor');
        });
    }
};

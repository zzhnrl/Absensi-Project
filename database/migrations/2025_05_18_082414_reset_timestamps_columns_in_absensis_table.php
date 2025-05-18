<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('absensis', function (Blueprint $table) {
            // Hapus dulu kolom integer lama
            $table->dropColumn(['created_at', 'updated_at']);
        });

        Schema::table('absensis', function (Blueprint $table) {
            // Tambah ulang sebagai TIMESTAMP dengan default CURRENT_TIMESTAMP
            $table->timestamp('created_at')
                  ->nullable()
                  ->useCurrent()
                  ->after('status_absen');  // letakkan setelah kolom status_absen

            $table->timestamp('updated_at')
                  ->nullable()
                  ->useCurrentOnUpdate()
                  ->after('created_at');
        });
    }

    public function down()
    {
        Schema::table('absensis', function (Blueprint $table) {
            // Drop kolom timestamp baru
            $table->dropColumn(['created_at', 'updated_at']);
        });

        Schema::table('absensis', function (Blueprint $table) {
            // Kembalikan ke integer (atau sesuai tipe lama)
            $table->integer('created_at')->default(0);
            $table->integer('updated_at')->default(0);
        });
    }
};

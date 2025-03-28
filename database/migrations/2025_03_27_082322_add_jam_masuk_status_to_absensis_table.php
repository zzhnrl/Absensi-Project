<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->time('jam_masuk')->nullable()->after('nama_karyawan');
            $table->string('status_absen')->nullable()->after('jam_masuk');
        });
    }

    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropColumn(['jam_masuk', 'status_absen']);
        });
    }
};


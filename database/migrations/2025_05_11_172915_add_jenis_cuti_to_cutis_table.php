<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cutis', function (Blueprint $table) {
            // letakkan setelah kolom yang sesuai, misal setelah tanggal_mulai
            $table->string('jenis_cuti')->after('tanggal_mulai')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('cutis', function (Blueprint $table) {
            $table->dropColumn('jenis_cuti');
        });
    }
};

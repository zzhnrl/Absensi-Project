<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePointUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('point_users', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->integer('user_id');
            $table->string('nama_karyawan'); 
            $table->string('bulan');
            $table->string('tahun');
            $table->integer('jumlah_point');
            $table->integer('is_active')->default(1);
            $table->integer('version')->default(0);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->integer('created_at')->nullable();
            $table->integer('updated_at')->nullable();
            $table->integer('deleted_at')->nullable();

            $table->index(['id']);
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('point_users');
    }
}

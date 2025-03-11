<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCutisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cutis', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->integer('user_id');
            $table->integer('status_cuti_id');
            $table->string('nama_karyawan');
            $table->date('tanggal_mulai'); 
            $table->date('tanggal_akhir'); 
            $table->string('keterangan')->nullable();
            $table->date('approve_at')->nullable();
            $table->integer('approve_by')->nullable();
            $table->date('reject_at')->nullable();
            $table->integer('reject_by')->nullable();
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
            $table->index(['status_cuti_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cutis');
    }
}

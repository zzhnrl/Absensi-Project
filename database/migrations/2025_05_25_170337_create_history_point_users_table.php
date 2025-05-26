<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoryPointUsersTable extends Migration
{
    public function up()
    {
        Schema::create('history_point_users', function (Blueprint $table) {
            // relasi ke users.id
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');

            // total poin user setelah perubahan
            $table->integer('jumlah_point');

            // perubahan poin: + untuk penambahan, - untuk pengurangan
            $table->integer('perubahan_point');
        });
    }

    public function down()
    {
        Schema::dropIfExists('history_point_users');
    }
}

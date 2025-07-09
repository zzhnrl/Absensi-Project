<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexToOfficeLocationsTable extends Migration
{
    public function up()
    {
        Schema::table('office_locations', function (Blueprint $table) {
            $table->unsignedTinyInteger('index')->unique()->after('id');
        });
    }

    public function down()
    {
        Schema::table('office_locations', function (Blueprint $table) {
            $table->dropColumn('index');
        });
    }

}

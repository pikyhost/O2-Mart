<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAttemptsColumnOnJobsTable extends Migration
{
    public function up()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->unsignedInteger('attempts')->default(0)->change();
        });
    }

    public function down()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->tinyInteger('attempts')->unsigned()->default(0)->change();
        });
    }
}

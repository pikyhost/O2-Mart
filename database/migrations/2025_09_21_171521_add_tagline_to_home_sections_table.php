<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('home_sections', function (Blueprint $table) {
            $table->string('tagline')->nullable();
        });
    }

    public function down()
    {
        Schema::table('home_sections', function (Blueprint $table) {
            $table->dropColumn('tagline');
        });
    }
};
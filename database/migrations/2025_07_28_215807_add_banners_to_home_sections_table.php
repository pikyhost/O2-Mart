<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('home_sections', function (Blueprint $table) {
            $table->string('banner_1_image')->nullable();
            $table->string('banner_1_link')->nullable();
            $table->string('banner_2_image')->nullable();
            $table->string('banner_2_link')->nullable();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('home_sections', function (Blueprint $table) {
            //
        });
    }
};

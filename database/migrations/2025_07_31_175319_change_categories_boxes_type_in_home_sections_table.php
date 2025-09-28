<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up()
    {
        Schema::table('home_sections', function (Blueprint $table) {
            $table->json('categories_boxes')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('home_sections', function (Blueprint $table) {
            $table->longText('categories_boxes')->nullable()->change();
        });
    }
};

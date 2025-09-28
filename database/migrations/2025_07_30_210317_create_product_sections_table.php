<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_sections', function (Blueprint $table) {
            $table->id();
            $table->string('type')->unique(); // auto_part, tyre, battery, rim
            $table->string('background_image')->nullable();

            $table->string('section1_title')->nullable();
            $table->text('section1_text1')->nullable();
            $table->text('section1_text2')->nullable();

            $table->string('section2_title')->nullable();
            $table->text('section2_text')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_sections');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('about_us', function (Blueprint $table) {
            $table->id();

            $table->string('slider_1_image')->nullable();
            $table->string('slider_2_image')->nullable();
            $table->string('intro_image')->nullable();
            $table->string('first_section_title')->nullable();
            $table->text('first_section_desc')->nullable();

            $table->string('intro_title');
            $table->text('intro_text');
            $table->string('intro_cta')->nullable();
            $table->string('intro_url')->nullable();

            $table->string('center_title');
            $table->text('center_text');
            $table->string('center_cta')->nullable();
            $table->string('center_url')->nullable();

            $table->string('latest_title');
            $table->text('latest_text');

            $table->string('about_us_video_path')->nullable();
            $table->string('center_image_path')->nullable();
            $table->string('latest_image_path')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('about_us');
    }
};

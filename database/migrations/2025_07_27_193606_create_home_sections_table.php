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
        Schema::create('home_sections', function (Blueprint $table) {
            $table->id();
            $table->string('section_1_title')->nullable();
            $table->text('section_1_text')->nullable();
            $table->string('section_1_image')->nullable();

            $table->string('section_2_title')->nullable();
            $table->text('section_2_text')->nullable();
            $table->string('section_2_image')->nullable();

            $table->string('section_3_title')->nullable();
            $table->text('section_3_text')->nullable();
            $table->string('section_3_image')->nullable();

            $table->string('section_4_image')->nullable();
            $table->string('section_4_link')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_sections');
    }
};

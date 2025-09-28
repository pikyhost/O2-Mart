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
        Schema::create('shop_pages', function (Blueprint $table) {
            $table->id();
            $table->string('section_1_title')->nullable();
            $table->text('section_1_content')->nullable(); 
            $table->string('section_2_title')->nullable();
            $table->text('section_2_content')->nullable(); 
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_pages');
    }
};

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
        Schema::create('brands', function (Blueprint $table) {
            $table->id();

            // Core brand information
            $table->string('name')->unique();
            $table->string('slug')->unique()->nullable();
            $table->text('description')->nullable();
            $table->string('website_url')->nullable();

            // Status & visibility
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_featured')->default(false)->index();

            // SEO fields
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();

            // Timestamps
            $table->timestamps();
            // Indexes
            $table->index(['name', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};

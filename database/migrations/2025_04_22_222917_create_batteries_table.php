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
        Schema::create('batteries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('brand_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('country_id')->nullable()->constrained()->onDelete('set null');

            $table->string('parent_category_name')->nullable();
            $table->string('sub_category_name')->nullable();
            $table->string('brand_name')->nullable();
            $table->string('country_name')->nullable();

            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            $table->string('warranty');
            $table->string('capacity');
            $table->string('dimensions');
            $table->decimal('weight', 6, 2);
            $table->string('item_code')->unique();
            $table->string('sku')->unique();
            $table->decimal('regular_price', 10, 2)->nullable();
            $table->unsignedTinyInteger('discount_percentage')->nullable();
            $table->decimal('discounted_price', 10, 2)->nullable();
            $table->string('image_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batteries');
    }
};

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
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // Foreign key relationships - all nullable
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('brand_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('country_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignIdFor(\App\Models\CarModel::class)->nullable()->constrained()->onDelete('set null');

            // Product type to distinguish between auto_parts, batteries, tyres
            $table->enum('product_type', ['auto_parts', 'batteries', 'tyres'])->nullable();

            // Category and brand information (unified)
            $table->string('parent_category_name')->nullable();
            $table->string('sub_category_name')->nullable();
            $table->string('brand_name')->nullable();
            $table->string('country_name')->nullable();
            $table->string('model')->nullable();

            // Basic product information (unified)
            $table->string('name')->nullable(); // unified name/title field
            $table->string('slug')->nullable()->unique();
            $table->string('sku')->nullable()->unique();
            $table->string('item_code')->nullable()->unique();
            $table->text('description')->nullable(); // unified description/details field

            // Dimensions and weight (unified)
            $table->float('weight')->nullable(); // unified weight field
            $table->float('height')->nullable();
            $table->float('width')->nullable();
            $table->float('length')->nullable();
            $table->string('dimensions')->nullable(); // alternative dimensions format

            // Product specific fields
            $table->string('viscosity_grade')->nullable(); // auto parts
            $table->string('warranty')->nullable(); // batteries & tyres
            $table->string('capacity')->nullable(); // batteries
            $table->string('tire_size')->nullable(); // tyres
            $table->string('wheel_diameter')->nullable(); // tyres
            $table->string('load_index')->nullable(); // tyres
            $table->string('speed_rating')->nullable(); // tyres
            $table->year('production_year')->nullable(); // tyres
            $table->string('tyre_oem')->nullable(); // tyres

            // Car compatibility fields
            $table->string('car_make')->nullable();
            $table->string('car_model')->nullable();
            $table->year('car_year')->nullable();
            $table->string('engine_performance')->nullable();

            // Pricing fields (unified)
            $table->decimal('regular_price', 10, 2)->nullable(); // unified price field
            $table->decimal('discount_percentage', 5, 2)->nullable(); // unified discount field
            $table->decimal('discounted_price', 10, 2)->nullable();

            // Image and media fields (unified)
            $table->string('image_url')->nullable(); // unified image field
            $table->string('image_alt_text')->nullable(); // unified alt text field

            $table->timestamps();

            // Indexes for better performance
            $table->index('product_type');
            $table->index('brand_id');
            $table->index('category_id');
            $table->index('country_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

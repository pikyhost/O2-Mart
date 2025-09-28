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
        Schema::create('auto_parts', function (Blueprint $table) {
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
            $table->float('weight')->nullable();
            $table->float('height')->nullable();
            $table->float('width')->nullable();
            $table->float('length')->nullable();
            $table->text('details')->nullable();
            $table->string('viscosity_grade')->nullable();
            $table->string('sku')->unique();
            $table->decimal('price_including_vat', 10, 2)->nullable();
            $table->decimal('discount_percentage', 5, 2)->nullable();
            $table->decimal('discounted_price', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->string('photo_alt_text')->nullable();
            $table->string('photo_link')->nullable();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auto_parts');
    }
};

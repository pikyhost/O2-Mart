<?php

use App\Models\MyModel;
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
        Schema::create('tyres', function (Blueprint $table) {
            $table->id();

            $table->foreignId('brand_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('country_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignIdFor(\App\Models\CarModel::class)->nullable()->constrained()->onDelete('set null');

            $table->string('brand');
            $table->string('country_of_origin')->nullable();
            $table->string('model')->nullable();

            $table->string('title');
            $table->text('image')->nullable();
            $table->string('slug')->unique();
            $table->string('sku')->unique();
            $table->text('description');
            $table->string('tire_size');
            $table->string('width')->nullable();
            $table->string('height')->nullable();
            $table->string('wheel_diameter')->nullable();
            $table->string('load_index')->nullable();
            $table->string('speed_rating')->nullable();
            $table->decimal('weight_kg', 8, 2)->nullable();
            $table->string('alt_text')->nullable();
            $table->year('production_year')->nullable();
            $table->string('warranty')->nullable();
            $table->decimal('price_vat_inclusive', 10, 2);
            $table->decimal('discounted_price', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tyres');
    }
};

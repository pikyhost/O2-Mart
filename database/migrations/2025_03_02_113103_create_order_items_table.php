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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete(); // Associated order
            $table->foreignIdFor(\App\Models\Product::class)->nullable()->constrained()->cascadeOnDelete(); // Single product
            $table->integer('quantity')->default(1); // Number of items ordered
            $table->unsignedInteger('price_per_unit'); // Price per product/bundle
            $table->unsignedInteger('subtotal'); // quantity * price_per_unit
            $table->timestamps();
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};

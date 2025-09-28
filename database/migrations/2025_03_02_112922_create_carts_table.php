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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); // Authenticated users
            $table->string('session_id')->nullable()->unique(); // Ensure guests have a unique session
            $table->unsignedInteger('subtotal')->default(0); // Sum of all cart items' subtotals
            $table->unsignedInteger('total')->default(0); // Final total after applying discounts
            $table->unsignedTinyInteger('tax_percentage')->default(0);
            $table->integer('tax_amount')->default(0);
            $table->foreignId('country_id')->nullable()->constrained('countries')->cascadeOnDelete();
            $table->foreignId('governorate_id')->nullable()->constrained('governorates')->cascadeOnDelete();
            $table->foreignId('city_id')->nullable()->constrained('cities')->cascadeOnDelete();
            $table->foreignId('area_id')->nullable()->constrained('areas')->cascadeOnDelete();
            $table->unsignedInteger('shipping_cost')->nullable(); // Optional, free shipping cases
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};

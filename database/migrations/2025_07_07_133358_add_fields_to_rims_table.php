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
        Schema::table('rims', function (Blueprint $table) {
            $table->string('alt_text')->nullable();
            $table->string('colour')->nullable();
            $table->string('condition')->nullable();
            $table->string('specification')->nullable();
            $table->string('bolt_pattern')->nullable();
            $table->string('offsets')->nullable();
            $table->string('centre_caps')->nullable();
            $table->foreignId('rim_attribute_id')->nullable()->constrained();
            $table->boolean('is_set_of_4')->default(false);
            $table->string('item_code')->nullable();
            $table->string('sku')->nullable();
            $table->string('warranty')->nullable();
            $table->decimal('weight', 8, 2)->nullable();
            $table->decimal('regular_price', 10, 2)->nullable();
            $table->decimal('discounted_price', 10, 2)->nullable();
            $table->decimal('discount_percent', 5, 2)->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rims', function (Blueprint $table) {
            //
        });
    }
};

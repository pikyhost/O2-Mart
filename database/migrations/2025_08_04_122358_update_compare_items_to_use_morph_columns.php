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
        Schema::table('compare_items', function (Blueprint $table) {
            $table->dropColumn(['product_type', 'product_id']);
            $table->string('buyable_type');
            $table->unsignedBigInteger('buyable_id');
        });
    }

    public function down(): void
    {
        Schema::table('compare_items', function (Blueprint $table) {
            $table->dropColumn(['buyable_type', 'buyable_id']);
            $table->string('product_type');
            $table->unsignedBigInteger('product_id');
        });
    }

};
